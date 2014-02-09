<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Service;

use Flower\View\Pane\Exception\RuntimeException;
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
abstract class AbstractCacheListener extends AbstractListenerAggregate implements CacheListenerInterface
{
    protected $serializer;

    protected $storage;

    protected $storageOptions;

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
    abstract public function attach(EventManagerInterface $events);
    
    public function setStorageOptions(array $storageOptions)
    {
        $this->storageOptions = $storageOptions;
    }

    public function getStorageOptions()
    {
        return $this->storageOptions;
    }

    /**
     * pass data to low level
     *
     *
     */
    public function setStorage(StorageInterface $storage = null)
    {
        if (null === $storage) {
            $storageOptions = $this->getStorageOptions();
            if (!is_array($storageOptions)) {
                return;
            }
            try {
                $storage = StorageFactory::factory($storageOptions);
            } catch (InvalidArgumentException $ex) {
                throw new RuntimeException('try to make a cache storage, but invalid options', $ex->getCode(), $ex);
            }
        }

        $this->storage = $storage;
    }

    public function getSerializer()
    {
        return $this->serializer;
    }

    public function setSerializer(AdapterInterface $serializer)
    {
        $this->serializer = $serializer;
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

}
