<?php

/*
 * 
 * @copyright Copyright (c) 2013-2015 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\AccessControl\AuthClient;

/**
 * Description of ResourceStorageAwareTrait
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
trait ResourceStorageAwareTrait {
    /**
     *
     * @var ResourceStorageInterface
     */
    protected $resourceStorage;
    
    /**
     * 
     * @param ResourceStorageInterface $resourceStorage
     */
    public function setResourceStorage(ResourceStorageInterface $resourceStorage)
    {
        $this->resourceStorage = $resourceStorage;
    }
    
    /**
     * 
     * @return ResourceStorageInterface
     */
    public function getResourceStorage()
    {
        return $this->resourceStorage;
    }
}
