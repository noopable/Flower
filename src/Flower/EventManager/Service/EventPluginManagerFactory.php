<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\EventManager\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class EventPluginManagerFactory
{

    const PLUGIN_MANAGER_CLASS = 'Flower\EventManager\Event\EventPluginManager';

    /**
     * Create and return a plugin manager.
     * Classes that extend this should provide a valid class for
     * the PLUGIN_MANGER_CLASS constant.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Zend\ServiceManager\AbstractPluginManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $pluginManagerClass = static::PLUGIN_MANAGER_CLASS;
        /* @var $plugins \Zend\ServiceManager\AbstractPluginManager */
        $plugins = new $pluginManagerClass;
        $plugins->setServiceLocator($serviceLocator);
        $configuration = $serviceLocator->get('Config');

        if (isset($configuration['di']) && $serviceLocator->has('Di')) {
            $plugins->addAbstractFactory($serviceLocator->get('DiAbstractServiceFactory'));
        }

        return $plugins;
    }
}
