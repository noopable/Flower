<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\EventManager\Service;

use Flower\File\Service\FileServiceFactoryFromConfig;

/**
 * Description of RegistryFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class RegistryFactory extends FileServiceFactoryFromConfig
{
    protected $configKey = 'flower_events_registry';
}
