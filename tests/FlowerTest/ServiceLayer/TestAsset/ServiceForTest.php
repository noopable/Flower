<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace FlowerTest\ServiceLayer\TestAsset;

use Flower\ServiceLayer\AbstractService;
/**
 * Description of ServiceForTest
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ServiceForTest extends AbstractService {
    
    public function __call($name, $arguments)
    {
        return array('name' => $name, 'arguments' => $arguments);
    }
}
