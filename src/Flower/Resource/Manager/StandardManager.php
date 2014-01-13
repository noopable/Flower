<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\Resource\Manager;

use Flower\Resource\Exception\RuntimeException;

use Flower\Resource\ResourceClass\ResourceInterface;
use Flower\Resource\Converter\DefaultConverter;
use Flower\Resource\Converter\PersistenceConverterInterface;
use Flower\Resource\Mapper\Mapper;
use Flower\Resource\Mapper\ResourceEntityMapperInterface;
use Flower\Resource\ResourceProviderInterface;


/**
 * Description of ResourceManagerByResourceMapper
 *
 * @author tomoaki
 */
class StandardManager implements BulkManagerInterface, ResourceEntityMapperInterface{
    use CacheStorageTrait;
    
    /**
     *
     * @var array
     */
    protected $bulkIds = array();
    
    /**
     *
     * @var array
     */
    protected $objects = array();
    
    protected $resources = array();
    
    /**
     *
     * @var mixed
     */
    protected $entity;
    
    /**
     *
     * @var PersistenceConverterInterface
     */
    protected $converter;
    
    /**
     *
     * @var ResourceEntityMapperInterface
     */
    protected $mapper;
    
    /**
     * configオブジェクトパターンによるconfiguration
     * @param \Flower\Resource\Manager\Config $config
     */
    public function __construct(Config $config = null)
    {
        if (null !== $config) {
            $config->configure($this);
        }
    }
    
    public function setConverter(PersistenceConverterInterface $converter)
    {
        $this->converter = $converter;
    }
    
    public function getConverter()
    {
        if (!isset($this->converter)) {
            $this->converter = new DefaultConverter;
        }
        return $this->converter;
    }
    
    public function setMapper(ResourceEntityMapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }
    
    public function getMapper()
    {
        if (!isset($this->mapper)) {
            $this->mapper = new Mapper;
        }
        return $this->mapper;
    }
    
    /**
     * 
     * @param any $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }
    
    /**
     * 
     * @param mixed $entity
     * @return string saved resourceId
     */
    public function save($entity = null)
    {
        $resource = $this->enResource($entity);
        $this->saveResource($resource);
        return $resource->getResourceId();
    }
    
    public function saveResource(ResourceInterface $resource)
    {
        /**
         * @var $type string class alias of class implements ResourceInterface
         */
        $kvPair = $this->getConverter()->resourceToKvPair($resource);
        $this->getStorage()->setItems($kvPair);
        return $resource->getResourceId();
    }
    
    /**
     * 
     * @param string $resourceId
     * @param boolean $refresh
     * @return ResourceInterface
     */
    public function get($resourceId, $refresh = false)
    {
        $storage = $this->getStorage();
        if (!$refresh && isset($this->resources[$resourceId])) {
            return $this->resources[$resourceId];
        }
        $keys = $this->getConverter()->resourceIdToKvKeys($resourceId);
        $kvPairs = $storage->getItems($keys);
        $resources = $this->getConverter()->kvPairsToResources($kvPairs);
        //キャッシュにヒットしなければflyweightしない。
        $resource = array_shift($resources);
        if ($resource instanceof ResourceInterface) {
            $this->resources[$resourceId] = $resource;
        }
        return $resource;
    }
    
    protected function enResource($entity = null)
    {
        if (null === $entity) {
            if (isset($this->entity)) {
                $entity = $this->entity;
            } else {
                throw new RuntimeException('not specified resource nor an entity');
            }
        }

        if ($entity instanceof ResourceProviderInterface) {
            return  $entity->getResource();
        }
        
        if ($entity instanceof ResourceInterface) {
            return $entity;
        }
        
        return $this->getMapper()->entityToResource($entity);
    }

    public function addBulkIds(array $resourceIds) 
    {
        if (!isset($this->bulkIds)) {
            $this->bulkIds = array();
        }
        $this->setBulkIds(array_merge($this->bulkIds, $resourceIds));
        return $this;
    }

    /**
     * 
     * @param type $refresh
     * @return array Associative array of keys and values
     */
    public function bulkGet($refresh = false) {
        if (!isset($this->bulkIds)) {
            return array();
        }
        return $this->getResources($this->bulkIds, $refresh);
    }

    public function getResources(array $resourceIds, $refresh = false) 
    {
        $storage = $this->getStorage();
        $objects = $this->resources;
        if ($refresh) {
            $this->resources = array();
            $ids = $resourceIds;
        } else {
            $newIdsCond = function ($v) use ($objects) {
                return is_string($v) && !array_key_exists($v, $objects);
            };
            $ids = array_filter((array) $resourceIds, $newIdsCond );
        }
        $keys = array();
        foreach ($ids as $resourceId) {
            $keys = array_merge($keys, array_values($this->getConverter()->resourceIdToKvKeys($resourceId)));
        }
        $kvPairs = $storage->getItems($keys);
        $newResources = $this->getConverter()->kvPairsToResources($kvPairs);
        foreach ($newResources as $newResource) {
            $this->resources[$newResource->getResourceId()] = $newResource;
        }
        $compares = array_flip($resourceIds);
        return array_intersect_key($this->resources, $compares);
    }

    public function setBulkIds(array $bulkIds) 
    {
        //文字列以外のときはWarningを吐いた上で、排除してくれる。
        //重複を排除してくれる。
        //@notice キー名がキー長以下になっていないとFileSystemには格納できない
        $this->bulkIds = array_flip(array_flip($bulkIds));
    }

    public function saveMulti(array $dataArray) 
    {
        $kvs = array();
        $targets = array();
        foreach ($dataArray as $val) {
            $resource = $this->enResource($val);
            if ($resource instanceof ResourceInterface) {
                $targets[] = $resource->getResourceId();
                $kvs = $kvs + $this->getConverter()->resourceToKvPair($resource);
            }
        }
        $this->getStorage()->setItems($kvs);
        return $targets;
    }

    /**
     * 
     * @param type $callback
     * @param type $entityClass
     * @param type $priority
     * @return \Flower\Resource\Manager\StandardManager
     */
    public function addStrategy($callback, $entityClass, $priority = 0)
    {
        $this->getMapper()->addStrategy($callback, $entityClass, $priority);
        return $this;
    }

    /**
     * 
     * @param type $entity
     * @return type
     */
    public function entityToResource($entity)
    {
        return $this->getMapper()->entityToResource($entity);
    }

    /**
     * 
     * @param array $strategies
     * @param type $refresh
     * @return type
     */
    public function setStrategies(array $strategies, $refresh = true)
    {
        return $this->getMapper()->setStrategies($strategies, $refresh);
    }

}
