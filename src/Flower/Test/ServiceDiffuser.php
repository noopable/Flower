<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Test;

use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use Zend\Mvc\Service\ServiceManagerConfig;

/**
 * test suite 全体のconfigをロードしておき
 * 各テストケースでは、部分的に上書きされたServiceManagerを取得します。
 *
 * 必要に応じて、Configを上書きすることも可能にします。
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ServiceDiffuser {

    protected $baseConfig = array(
        'service_manager' => array(),
        'modules' => array(
            'Flower',
        ),
        'module_listener_options' => array(
            'module_paths' => array(
                'module',
                'vendor',
            ),
        ),
    );

    public function __construct($baseConfig = null)
    {
        if (is_string($baseConfig)) {
            if (is_file($baseConfig) && is_readable(($baseConfig))) {
                $baseConfig = include $baseConfig;
            }
        }

        if (is_array($baseConfig)) {
            $this->baseConfig = ArrayUtils::merge($this->baseConfig, $baseConfig);
        }
    }

    public function diffuseServiceLocator(array $appConfig = array(), array $moduleConfig = null)
    {
        $mergedAppConfig = ArrayUtils::merge($this->baseConfig, $appConfig);
        $serviceManager = new ServiceManager(new ServiceManagerConfig($mergedAppConfig['service_manager']));
        $serviceManager->setService('ApplicationConfig', $mergedAppConfig);
        $serviceManager->get('ModuleManager')->loadModules();
        $ref = new \ReflectionObject($serviceManager);
        $prop = $ref->getProperty('allowOverride');
        $prop->setAccessible(true);
        $prop->setValue($serviceManager, true);

        if (null !== $moduleConfig) {
            $origConfig = $serviceManager->get('Config');
            $moduleConfig = ArrayUtils::merge($origConfig, $moduleConfig);
            $serviceManager->setService('Config', $moduleConfig);
        }

        return $serviceManager;
    }
}
