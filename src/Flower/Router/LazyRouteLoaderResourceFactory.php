<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Router;

use Flower\Resource\ResourceManagerFactory;
/**
 * Description of LazyRouteLoaderFileFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class LazyRouteLoaderResourceFactory extends ResourceManagerFactory {
    protected $configId = 'flower_lazy_load_route_resource';
}
