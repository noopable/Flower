<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Service;

use Flower\File\Service\FileServiceFactoryFromConfig;

/**
 * Description of ConfigFileServiceFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ConfigFileServiceFactory extends FileServiceFactoryFromConfig  {

    protected $configKey = 'pane_config_file';

}
