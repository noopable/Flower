<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Service;

use Flower\View\Pane\Exception\RuntimeException;
use Flower\View\Pane\PaneEvent;
use Zend\Cache\Exception\InvalidArgumentException;
use Zend\Cache\StorageFactory;
use Zend\Cache\Storage\StorageInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Serializer\Adapter\AdapterInterface;

/**
 * Description of ConfigFileListener
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class RenderCacheListener extends AbstractListenerAggregate implements CacheListenerInterface
{
    protected $serializer;

    protected $storage;

    protected $storageOptions;

    public function setStorageOptions(array $storageOptions)
    {
        $this->storageOptions = $storageOptions;
    }

    /**
     * pass data to low level
     *
     *
     */
    public function setStorage(StorageInterface $storage = null)
    {
        if (null === $storage) {
            if (!isset($this->storageOptions)) {
                return;
            }
            try {
                $storage = StorageFactory::factory($this->storageOptions);
            } catch (InvalidArgumentException $ex) {
                throw new RuntimeException('try to make a cache storage, but invalid options', $ex->getCode(), $ex);
            }
        }

        $this->storage = $storage;
    }

    /**
     *
     *
     * @return StorageInterface
     */
    public function getStorage()
    {
        if (!isset($this->storage)) {
            $this->setStorage();
        }
        return $this->storage;
    }

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(PaneEvent::EVENT_RENDER, array($this, 'preRender'), 10000);
        $this->listeners[] = $events->attach(PaneEvent::EVENT_RENDER, array($this, 'postRender'), -10000);
    }

    public function preRender(PaneEvent $e)
    {
        if (!$storage = $this->getStorage()) {
            return;
        }

        $paneId = $e->getPaneId();

        if (! $storage->hasItem($paneId)) {
            return;
        }

        try {
            $rendered = $storage->getItem($paneId);
        } catch (\Exception $ex) {
            $e->addErrorMessage($ex->getMessage() . ' at ' . $ex->getFile() . ' : ' . $ex->getLine());
            $storage->removeItem($paneId);
            return;
        }

        $e->setResult($rendered);

        $e->stopPropagation(true);

        return $rendered;
    }

    public function postRender(PaneEvent $e)
    {
        if (!$e->hasResult()) {
            return;
        }

        $rendered = $e->getResult();
        $paneId = $e->getPaneId();

        if ($e->hasError()) {
            return $rendered;
        }

        if (!$storage = $this->getStorage()) {
            return $rendered;
        }

        try {
            $storage->setItem($paneId, $rendered);
        } catch (\Exception $ex) {
            $e->addErrorMessage($ex->getMessage() . ' at ' . $ex->getFile() . ' : ' . $ex->getLine());
        }

        return $rendered;
    }

    public function getSerializer()
    {
        return $this->serializer;
    }

    public function setSerializer(AdapterInterface $serializer)
    {
        $this->serializer = $serializer;
    }

}
