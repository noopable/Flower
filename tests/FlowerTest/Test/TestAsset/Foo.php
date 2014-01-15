<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace FlowerTest\Test\TestAsset;

/**
 * Description of Foo
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class Foo {
    protected $bar = 'value';
    
    protected static $baz = 'staticValue';
    
    protected function doMethod()
    {
        return __METHOD__;
    }
}
