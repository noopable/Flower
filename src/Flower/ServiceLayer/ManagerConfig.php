<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\ServiceLayer;

use Zend\ServiceManager\Config;

/**
 * Description of ManagerConfig
 *
 * @author tomoaki
 */
class ManagerConfig extends Config{
    public function configure(ServiceLayerPluginManager $manager)
    {
        parent::configureServiceManager($manager);
        
    }
}
