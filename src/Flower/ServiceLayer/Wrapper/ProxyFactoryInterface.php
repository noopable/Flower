<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\ServiceLayer\Wrapper;

use Flower\ServiceLayer\ServiceLayerInterface;
/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface ProxyFactoryInterface {
    /**
     * 
     * @return ServiceLayerInterface
     */
    public function factory(ServiceLayerInterface $service);
}
