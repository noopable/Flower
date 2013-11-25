<?php
namespace Flower\File\Gateway;
/*
 * 
 * 
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

/**
 * 公開インターフェース
 * 
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface GatewayInterface {
    
    /**
     * 
     * @param string|\Flower\File\Event $name
     * @param flags $resolveMode
     * @return string
     */
    public function resolve($nameOrEvent, $resolveMode = null);
    
    /**
     * 
     * @param name $name
     * @return data
     */
    public function read($name);
    
    /**
     * write data to specified name
     * 
     * @param string $name
     * @param any $data
     * @param string $extension
     */
    public function write($name, $data, $extension = null);
    
    /**
     * clear cache or deliver files to slave server etc. 
     * 
     * @param type $name
     */
    public function refresh($name = null);
}
