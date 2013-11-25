<?php
namespace Flower\File\Resolver;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface ResolverInterface {
    
    /**
     * 
     * @param string $name
     * @param callable| null $callback
     * @return \Flower\File\FileInfo[]
     */
    public function resolve($name, $callback = null);
    

}

