<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Service;

use Flower\View\Pane\Exception\RuntimeException;
use Flower\View\Pane\PaneClass\PaneInterface;
use Flower\View\Pane\PaneEvent;
use Zend\Cache\Exception\InvalidArgumentException;
use Zend\Cache\StorageFactory;
use Zend\Cache\Storage\StorageInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Serializer\Adapter\PhpSerialize;
use Zend\Serializer\Adapter\AdapterInterface;

/**
 * Description of ConfigFileListener
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class PaneCacheListener extends AbstractListenerAggregate
{
    protected $serializer;

    protected $storage;

    protected $storageOptions;

    public function setStorageOptions($storageOptions)
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

    public function setSerializer(AdapterInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     *
     * @return AdapterInterface
     */
    public function getSerializer()
    {
        if (!isset($this->serializer)) {
            $this->serializer = new PhpSerialize;
        }
        return $this->serializer;
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
        $this->listeners[] = $events->attach(PaneEvent::EVENT_GET_PANE, array($this, 'preGet'), 10000);
        $this->listeners[] = $events->attach(PaneEvent::EVENT_GET_PANE, array($this, 'postGet'), -10000);
    }

    public function preGet(PaneEvent $e)
    {
        if (!$storage = $this->getStorage()) {
            return;
        }

        $paneId = $e->getPaneId();

        if (! $storage->hasItem($paneId)) {
            return;
        }

        try {
            $serialized = $storage->getItem($paneId);
            $pane = $this->getSerializer()->unserialize($serialized);
        } catch (\Exception $ex) {
            $e->addErrorMessage($ex->getMessage() . ' at ' . $ex->getFile() . ' : ' . $ex->getLine());
            $storage->removeItem($paneId);
            return;
        }

        if (! $pane instanceof PaneInterface ){
            $e->addErrorMessage('failed to make pane from cached string ');
            $storage->removeItem($paneId);
            return;
        }

        $e->setResult($pane);

        $e->stopPropagation(true);

        return $pane;
    }

    public function postGet(PaneEvent $e)
    {
        if (!$e->hasResult()) {
            return;
        }

        $pane = $e->getResult();
        $paneId = $e->getPaneId();

        if (!$pane instanceof PaneInterface) {
            return;
        }

        if ($e->hasError()) {
            return $pane;
        }

        if (!$storage = $this->getStorage()) {
            return $pane;
        }

        try {
            $serialized = $this->getSerializer()->serialize($pane);
        } catch (\Exception $ex) {
            $e->addErrorMessage($ex->getMessage() . ' at ' . $ex->getFile() . ' : ' . $ex->getLine());
            //have callback?
            return $pane;
        }

        try {
            $storage->setItem($paneId, $serialized);
        } catch (\Exception $ex) {
            $e->addErrorMessage($ex->getMessage() . ' at ' . $ex->getFile() . ' : ' . $ex->getLine());
        }

        return $pane;
    }
}
