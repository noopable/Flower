<?php
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\Resource\ResourceClass;
/*
 * Resource feeds raw materials for document. 
 *  
 * Zend\Acl\Resourceとは違う、Documentの情報源としてのリソース
 * ファイルまたはRedisのようなKVSに保存する。
 * Zend\Cacheを利用してもよい。
 * 
 */

/**
 *
 * @author tomoaki
 */
interface ResourceInterface {

    /**
     * グローバルで一意となるID　
     *  ($type_$innerIdを推奨)
     * 
     */
    public function getResourceId();
    
    public function setResourceId($resourceId);
    /**
     * $typeスコープ内で一意となるID　
     * （10進数値表現文字列を推奨）
     * 
     */
    public function getInnerId();
    public function setInnerId($innerId);
    
    public function setType($type);
    public function getType();
    
    public function setOptions(array $options);
    public function getOptions();
    
    public function setProperties(array $properties);
    public function getProperties();
    
    public function setData($data, $serialized = false);
    public function getData($serialize = false);
    
    public function toString();
}
