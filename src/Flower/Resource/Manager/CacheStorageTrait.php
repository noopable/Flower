<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\Resource\Manager;

use Flower\Resource\Exception\RuntimeException;
use Zend\Cache;
/**
 * Description of ResourceManagerByCache
 *
 * @author tomoaki
 */
trait CacheStorageTrait {
    
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
    public function setStorage(Cache\Storage\StorageInterface $storage = null)
    {
        if (null === $storage) {
            if (!isset($this->storageOptions)) {
                throw new RuntimeException('try to make a cache storage, but missing cache storage options');
            }
            try {
                $storage = Cache\StorageFactory::factory($this->storageOptions);
            } catch (Cache\Exception\InvalidArgumentException $ex) {
                throw new RuntimeException('try to make a cache storage, but invalid options', 0, $ex);
            }
        }
        
        $this->storage = $storage;
    }
    
    /**
     * 
     * 
     * @return Cache\Storage\StorageInterface
     */
    public function getStorage()
    {
        if (!isset($this->storage)) {
            $this->setStorage();
        }
        return $this->storage;
    }
}
