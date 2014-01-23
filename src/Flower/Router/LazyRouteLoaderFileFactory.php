<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Router;

use Flower\File\Service\FileServiceFactoryFromConfig;
/**
 * Description of LazyRouteLoaderFileFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class LazyRouteLoaderFileFactory extends FileServiceFactoryFromConfig {
    protected $configKey = 'flower_lazy_load_route_file';
}
