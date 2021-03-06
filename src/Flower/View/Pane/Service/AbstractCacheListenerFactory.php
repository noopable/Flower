<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Service;

use Flower\Domain\Service as DomainService;
use Flower\Domain\DomainServiceAwareInterface;
use Zend\Cache\Storage\StorageInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of AbstractCacheListenerFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
abstract class AbstractCacheListenerFactory implements FactoryInterface
{
    protected $configKey = 'pane_cache_listener';

    protected $defaultListenerClass = 'Flower\View\Pane\ManagerListener\PaneCacheListener';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if (!$serviceLocator->has('Config')) {
            return;
        }

        $moduleConfig = $serviceLocator->get('Config');

        if (!isset($moduleConfig[$this->configKey])) {
            return;
        }

        $config = $moduleConfig[$this->configKey];

        return $this->createListener($serviceLocator, $config);
    }

    public function createListener($serviceLocator, $config)
    {
        if (isset($config['listener_class'])) {
            $listenerClass = $config['listener_class'];
        } else {
            $listenerClass = $this->defaultListenerClass;
        }
        $listener = new $listenerClass;

        if ($listener instanceof ServiceLocatorAwareInterface) {
            $listener->setServiceLocator($serviceLocator);
        }

        if (isset($config['cache_storage'])) {
            $this->configureCacheStorage($listener, $config['cache_storage']);
            unset($config['cache_storage']);
        }

        if (isset($config['serializer'])) {
            $this->configureSerializer($listener, $config['serializer']);
            unset($config['serializer']);
        }

        if (!empty($config)) {
            $this->configureExtra($serviceLocator, $listener, $config);
        }

        return $listener;
    }

    public function configureCacheStorage($listener, $options)
    {

        if ($options instanceof StorageInterface) {
            $listener->setStorage($options);
        }

        if (is_array($options)) {
            $listener->setStorageOptions($options);
        }
    }

    public function configureSerializer($listener, $serializer)
    {
        if (is_a($serializer, 'Zend\Serializer\Adapter\AdapterInterface', true)) {
            if (is_string($serializer)) {
                $serializer = new $serializer;
            }
            $listener->setSerializer($serializer);
        }
    }

    public function configureExtra($serviceLocator, $listener, $config)
    {
        if (isset($config['domain_service'])
            && ($listener instanceof DomainServiceAwareInterface)) {
            if (is_string($config['domain_service'])
                && $serviceLocator->has($config['domain_service'])) {
                $config['domain_service'] = $serviceLocator->get($config['domain_service']);
            }

            if ($config['domain_service'] instanceof DomainService) {
                $listener->setDomainService($config['domain_service']);
                unset($config['domain_service']);
            }
        }

    }
}
