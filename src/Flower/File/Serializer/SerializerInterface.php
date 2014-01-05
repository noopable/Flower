<?php
namespace Flower\File\Serializer;
/*
 * 
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface SerializerInterface
{
    public function serialize($data);
    
    public function unserialize($serialized);
    
    public function test($data);
            
}
