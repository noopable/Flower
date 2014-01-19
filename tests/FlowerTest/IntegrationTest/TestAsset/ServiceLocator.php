<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace FlowerTest\IntegrationTest\TestAsset;

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;
/**
 * Description of ServiceLocator
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ServiceLocator {

    public static function getServiceLocator(array $config)
    {
        $testConfig = include __DIR__ . '/config/TestConfig.php.dist';
        $config = $testConfig + $config;
        $serviceManager = new ServiceManager(new ServiceManagerConfig($config['service_manager']));
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();
        $ref = new \ReflectionObject($serviceManager);
        $prop = $ref->getProperty('allowOverride');
        $prop->setAccessible(true);
        $prop->setValue($serviceManager, true);
        $origConfig = $serviceManager->get('Config');
        $config = array_merge($origConfig, $config);
        $serviceManager->setService('Config', $config);
        return $serviceManager;
    }
}
