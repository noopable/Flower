<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\Resource\Mapper;

use Zend\Stdlib\PriorityQueue;
use Flower\Model\AbstractEntity;
use Flower\Resource\Exception\RuntimeException;
use Flower\Resource\ResourceClass\ResourceInterface;
use Flower\Resource\ResourceClass\Object as ObjectResource;
use Flower\Resource\ResourceClass\PhpArray;
use Flower\Resource\ResourceClass\Scalar;
use Flower\Resource\ResourceProviderInterface;

/**
 * エンティティをリソースに変換します。
 * エンティティの変換ルールはエンティティを管理している側から供給するようにしてください。
 * 汎用的なルールは正しくリソースを作成・管理できるものではなく簡易的なものです。
 * 
 * リソースの復元は復元方法が固定的でない以上、不完全な実装が害となることもあるので、
 * リソースはリソースとして使うのみで、エンティティへの可逆変換はリソースを取得した側で考えてください。
 *
 * @author tomoaki
 */
class Mapper implements ResourceEntityMapperInterface {

    /**
     *
     * @var PriorityQueue
     */
    protected $strategies;
    
    /**
     * IDの安全性は保証されないが自動処理を行う。
     * 
     * @var string
     */
    protected $resourceIdDelimiter = '_';
    
    public function __construct(array $options = null)
    {
        $strategies = isset($options['strategies']) ? $options['strategies'] : array();
        $this->setStrategies($strategies, true);
    }
    
    public function addStrategy($callback, $entityClass, $priority = 0) {
        if (!is_callable($callback)) {
            throw new RuntimeException('strategy should have callable');
        }
        $data['callback'] = $callback;
        //feeded prototype object
        if (is_object($entityClass)) {
            $entityClass = get_class($entityClass);
        }
        $data['entity'] = $entityClass;
        $this->strategies->insert($data, $priority);
        return $this;
    }
    
    public function setStrategies(array $strategies, $refresh = true) {
        if ($refresh) {
            $this->strategies = new PriorityQueue;
        }
        foreach ($strategies as $strategy) {
            /**
             * $strategy should be array [callback, className, priority = optional]
             * 
             */
            call_user_func_array(array($this, 'addStrategy'), $strategy);
        }
        return $this;
    }

    public function entityToResource($entity) {
        if (is_object($entity)) {
            
            //double check is necessary for independency to the manager.
            
            if ($entity instanceof ResourceProviderInterface) {
                return  $entity->getResource();
            }
            
            // Why don't you use php generator? yeild iiyo.
            if ($entity instanceof ResourceInterface) {
                return $entity;
            }
            
            $resource = $this->objectToResourceWithStrategy($entity);
            
            if ($resource instanceof ResourceInterface) {
                return $resource;
            }
            
            $resource = $this->objectToResourceWithGeneric($entity);
            
            if ($resource instanceof ResourceInterface) {
                return $resource;
            }
        }
        
        if (is_scalar($entity)) {
            //integer、float、string あるいは boolean
            $resource = new Scalar;
            $resource->setData($entity);
            return $resource;
        }
        
        if (is_array($entity)) {
            $resource = new PhpArray;
            $resource->setData($entity);
            return $resource;
        }
        
    }

    /**
     * not interface method use carefully
     * @param type $object
     * @return ResourceInterface|null
     */
    public function objectToResourceWithStrategy($object)
    {
        //PriorityQueue iterator foreach
        foreach ($this->strategies as $strategy) {
            if ($object instanceof $strategy['entity']) {
                return call_user_func($strategy['callback'], $object);
            }
        }
        return null;
    }
    
    protected function objectToResourceWithGeneric($object)
    {
        //generic auto detection enResource
        $resource = new ObjectResource;
        $resource->setData($object);
        $resource->setType(get_class($object));

        //resourceId detection
        if (isset($object->resourceId)) {
            $resourceId = $object->resourceId;
        } elseif (isset($object['resourceId'])) {
            $resourceId = $object['resourceId'];
        } 

        if ($object instanceof AbstractEntity) {
            $identifiers = $object->getIdentifier();
            sort($identifiers);
            foreach ($identifiers as $key) {
                $ids[$key] = isset($object->$key) ? (string) $object->$key : '';
            }
            $idsString = implode($this->resourceIdDelimiter, $ids);
            $innerId = sprintf("%u", crc32($idsString));
            $resource->setInnerId($innerId);
        }

        return $resource;
    }

}
