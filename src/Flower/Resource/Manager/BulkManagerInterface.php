<?php
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\Resource\Manager;
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
 * ResourceProviderはエンティティそのものがリソースを提供できる場合の実装
 * 
 * @author tomoaki
 */
interface BulkManagerInterface extends ManagerInterface {
    
    public function setBulkIds(array $bulkIds);
    
    public function addBulkIds(array $resourceIds);
    
    public function bulkGet($refresh = false);

    /**
     * 指定されたリソースリストから、未取得のリソースを取得し、
     * 要求されたキー配列にマッチするものを返す。
     * 
     */
    public function getResources(array $resourceIds, $refresh = false);
    
    public function saveMulti(array $dataArray);
}
