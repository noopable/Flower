<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\EventManager\Service;

use Flower\EventManager\Exception\RuntimeException;
use Flower\EventManager\Event\EventAbstractFactory;
use Flower\EventManager\Listener\CallbackSharedListenerAggregate;
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

        //自動的にStaticEventManagerから取得するので、通常、このブロックは不要。
        //Staticではない使用を意図する場合、逆にsetSharedManagerでStaticへの副作用が
        //生じるので、ここは妥協するよりない。

        if ($serviceLocator->has('SharedEventManager')) {
            $eventManager = $manager->getEventManager();
            $sharedEventManager = $serviceLocator->get('SharedEventManager');
            $eventManager->setSharedManager($sharedEventManager);
        }

        if (isset($config['callbacks'])) {
            if (!isset($sharedEventManager)) {
                if (!isset($eventManager)) {
                    $eventManager = $manager->getEventManager();
                }
                $sharedEventManager = $eventManager->getSharedManager();
            }
            $aggregate = $this->getAggregate($serviceLocator, $config['callbacks']);
            $sharedEventManager->attachAggregate($aggregate);
        }

        $manager->setEventPluginManager($eventPluginManager);
        $manager->setRegistry($serviceLocator->get($this->registryServiceName));

        return $manager;
    }

    protected function getAggregate($serviceLocator, $callbacks)
    {
        $aggregate = null;

        foreach ($callbacks as $config) {
            if (!isset($config['identifier'])
                || !isset($config['event'])
                || !isset($config['callback'])) {
                continue;
            }
            $callback = $this->parseCallback($serviceLocator, $config);
            if (!$callback) {
                continue;
            }
            if (!isset($aggregate)) {
                $aggregate = new CallbackSharedListenerAggregate;
            }
            $aggregate->addCallback($config['identifier'], $config['event'], $callback);
        }

        return $aggregate;
    }

    protected function parseCallback($serviceLocator, $config)
    {
        if (is_callable($config['callback'])) {
            return $config['callback'];
        }

        if (is_string($config['callback'])) {
            if ($serviceLocator->has($config['callback'])) {
                $callbackService = $serviceLocator->get($config['callback']);
            }
        }

        if (isset($config['callback_method'])) {
            $tmp = array($config['callback'], $config['callback_method']);
            if (is_callable($tmp)) {
                return $tmp;
            }
            if (isset($callbackService)) {
                $tmp = array($callbackService, $config['callback_method']);
                if (is_callable($tmp)) {
                    return $tmp;
                }
            }
        }

        if (isset($callbackService) && is_callable($callbackService)) {
            return $callbackService;
        }
    }
}
