<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\ServiceLayer\Wrapper;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface ServiceWrapperInterface {
    /**
     * 
     * @param object|\Flower\ServiceLayer\ServiceLayerInterface $instance
     * @param string|null $name
     * @return object
     */
    public function wrap($instance, $name = null);
}
