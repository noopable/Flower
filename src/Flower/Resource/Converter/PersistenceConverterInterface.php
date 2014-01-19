<?php
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\Resource\Converter;
/*
 *  
 * 変換ポリシー
 * 
 * KVSに保存する際の変換
 * KVSから取得後に復元するための変換
 * 
 */
use Flower\Resource\ResourceClass\ResourceInterface;
use Flower\Resource\ResourcePluginManager;
/**
 *
 * ResourceProviderはエンティティそのものがリソースを提供できる場合の実装
 * 
 * @author tomoaki
 */
interface PersistenceConverterInterface{
    
    /**
     * 保存しようとするリソースをキーバリューペアに変換する
     * 
     * @param $resource ResourceInterface
     * @return array for Zend\Cache\Storage::setItems
     */
    public function resourceToKvPair(ResourceInterface $resource);
    
    /**
     * ストレージから取得したKVペアをリソースリストに変換
     * 
     * @param array $kvPairs
     * @return ResourceInterface[] 
     */
    public function kvPairsToResources(array $kvPairs);
    /**
     * 連想配列をリソースに変換する
     * @param array $kvAssoc
     * @return ResourceInterface
     */
    public function AssocToResource(array $kvAssoc);
    
    /**
     * ストレージから取得したKVペアを連想配列に変換
     * 
     * @param array key value pair from KVS
     * @return array assoc for resource
     */
    public function kvPairsToAssocs(array $kvPairs);
    
    /**
     * For resource search by resourceId
     * key convert support
     *  This is reverse method of ResourceInterface::getResourceId
     * 
     * #check resourceId satisfy key pattern spec
     * 
     * @param string $resourceId global resource identifier
     * @param string|null $criteria return only criteria scope keyName
     * @return array keys of key value store search
     */
    public function resourceIdToKvKeys($resourceId, $criteria = null);

    public function setKeyPattern($keyPattern);
    
    public function getKeyPattern();
    
    /**
     * 
     * @param type ResourcePluginManager
     */
    public function setResourcePluginManager(ResourcePluginManager $pluginManager);
    
    /**
     * 
     * @return ResourcePluginManager;
     */
    public function getResourcePluginManager();
    
    
}
