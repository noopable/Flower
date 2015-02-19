<?php
/**
 *
 *
 * @copyright Copyright (c) 2013-2015 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\AccessControl;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of BlockPluginManagerFactory
 *
 * @author tomoaki
 */
class AccessControlServiceFactory implements FactoryInterface {
    protected $configId = 'flower_access_control';
    
    protected $serviceClass = 'Flower\AccessControl\AccessControlService';
    /**
     * Create and return abstract factory seeded by dependency injector
     *
     * Creates and returns an abstract factory seeded by the dependency
     * injector. If the "di" key of the configuration service is set, that
     * sub-array is passed to a DiConfig object and used to configure
     * the DI instance. The DI instance is then used to seed the
     * DiAbstractServiceFactory, which is then registered with the service
     * manager.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Di
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = array();
        if ($serviceLocator->has('Config')) {
            $globalConfig = $serviceLocator->get('Config');
            if (isset($globalConfig[$this->configId])) {
                $config =  $globalConfig[$this->configId];
            }
        }
        
        if ((!isset($config['use_di']) || (false !== $config['use_di'])) && $serviceLocator->has('Di')) {
            $di = $serviceLocator->get('Di');
            $service = $di->get($this->serviceClass);
        }

        if (!isset($service)) {
            $service = new $this->serviceClass;
        }
        
        if (!empty($config)) {
            if (!isset($config['service_locator'])) {
                $config['service_locator'] = $serviceLocator;
            }
            $serviceConfig = new ServiceConfig($config);
            $serviceConfig->configure($service);
        }

        return $service;
    }
}
