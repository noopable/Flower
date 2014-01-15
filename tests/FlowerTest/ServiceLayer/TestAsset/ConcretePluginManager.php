<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace FlowerTest\ServiceLayer\TestAsset;

use Zend\ServiceManager\AbstractPluginManager;
/**
 * Description of ConcretePluginManager
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ConcretePluginManager extends AbstractPluginManager{
    public function validatePlugin($instance)
    {
        return true;
    }
}
