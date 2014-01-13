<?php
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\Resource;
/*
 * Resource Provider generates its resource of it or another.
 *  
 * Zend\Acl\Resourceとは違う、Documentの情報源としてのリソース
 * ファイルまたはRedisのようなKVSに保存する。
 * Zend\Cacheを利用してもよい。
 * 
 * 
 */


/**
 *
 * @author tomoaki
 */
interface ResourceProviderInterface {
    public function getResource();
    
}
