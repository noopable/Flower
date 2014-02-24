<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\EventManager\Service;

use Flower\EventManager\Exception\RuntimeException;
use Flower\EventManager\Event\EventAbstractFactory;
use Flower\EventManager\RegistryEventManager;
use Zend\ServiceManager\Config as ServiceConfig;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of RegistryEventManagerFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class RegistryEventManagerFactory implements FactoryInterface
{
    protected $configKey = 'flower_registry_event_manager';

    protected $registryServiceName = 'Flower_Event_Registry';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if (!$serviceLocator->has('Config')
            || !isset($serviceLocator->get('Config')[$this->configKey])) {
                return;
        }

        $config = $serviceLocator->get('Config')[$this->configKey];

        if (! $serviceLocator->has($this->registryServiceName)) {
            throw new RuntimeException('EventManager needs registry service');
        }

        $manager = new RegistryEventManager;
        $eventPluginManagerFactory = new EventPluginManagerFactory;
        $eventPluginManager = $eventPluginManagerFactory->createService($serviceLocator);
        $abstractEventFactory = new EventAbstractFactory;
        $eventPluginManager->addAbstractFactory($abstractEventFactory);
        if (isset($config['event_plugins'])) {
            $serviceConfig = new ServiceConfig($config['event_plugins']);
            $serviceConfig->configureServiceManager($eventPluginManager);
            if (isset($config['event_plugins']['classes'])) {
                foreach ((array) $config['event_plugins']['classes'] as $class) {
                    $abstractEventFactory->addClass($class);
                }
            }
        }
        
        $manager->setEventPluginManager($eventPluginManager);
        $manager->setRegistry($serviceLocator->get($this->registryServiceName));

        return $manager;
    }

}
