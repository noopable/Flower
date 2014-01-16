<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace FlowerTest\ServiceLayer\Wrapper\TestAsset;

use Flower\ServiceLayer\Wrapper\AbstractProxy;
/**
 * Description of ConcreteProxy
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ConcreteProxy extends AbstractProxy {

    public function proxy($name, $arguments)
    {
        return call_user_func_array(array($this->innerObject, $name), $arguments);
    }
}
