<?php
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\Resource\Mapper;
/*
 * Resource feeds raw materials for document. 
 *  
 * Zend\Acl\Resourceとは違う、Documentの情報源としてのリソース
 * ファイルまたはRedisのようなKVSに保存する。
 * Zend\Cacheを利用してもよい。
 * 
 */
use Flower\Resource\ResourceClass\ResourceInterface;

/**
 *
 * @author tomoaki
 */
interface ResourceEntityMapperInterface{
    public function setStrategies(array $strategies, $refresh = true);
    public function addStrategy($callback, $entityClass, $priority = 0);
    
    /**
     * @param mixed $entity
     * @return ResourceInterface
     */
    public function entityToResource($entity);
    
}
