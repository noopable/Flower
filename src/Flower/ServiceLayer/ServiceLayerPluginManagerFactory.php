<?php
/**
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\ServiceLayer;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Di\DiAbstractServiceFactory;
use Zend\ServiceManager\Di\DiServiceInitializer;

/**
 * Description of BlockPluginManagerFactory
 *
 * @author tomoaki
 */
class ServiceLayerPluginManagerFactory implements FactoryInterface {

    protected $pluginClass = 'Flower\ServiceLayer\ServiceLayerPluginManager';

    protected $configId = 'flower_service_layer';
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
        $config = $serviceLocator->has('Config') ? $serviceLocator->get('Config') : array();

        /**
         * use di?
         *  オプションベースでDIの使用不使用をわけるよりも、Factoryを切り替えた方がいいと思います。
         *
         */
        $pluginManager = new $this->pluginClass;

        if (isset($config[$this->configId])) {
            $managerConfig = new ManagerConfig($config[$this->configId]);
            $managerConfig->configure($pluginManager);
        }

        if ($serviceLocator->has('Flower_AccessControl')) {
            $accessControlService = $serviceLocator->get('Flower_AccessControl');
            $pluginManager->addServiceWrapper($accessControlService);
        }

        if ($serviceLocator->has('Di')) {
            $di = $serviceLocator->get('Di');
            $pluginManager->addAbstractFactory(
                new DiAbstractServiceFactory($di, DiAbstractServiceFactory::USE_SL_NONE)
            );
            $pluginManager->addInitializer(
                new DiServiceInitializer($di, $serviceLocator)
            );
        }

        $pluginManager->setServiceLocator($serviceLocator);

        return $pluginManager;
    }
}
