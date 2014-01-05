<?php
namespace Flower\File\Spec\Cache;
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
interface CacheSpecInterface
{
    public function configure();
    
    public function getCacheFileName($name);
     
    public function getCacheExtension();
    
    public function cacheEnabled();
    
    public function getCachePath();
    
}
