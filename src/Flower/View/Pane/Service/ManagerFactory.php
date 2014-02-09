<?php

/**
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Service;

use Flower\View\Pane\Exception\RuntimeException;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;

/**
 * Description of ManagerFactory
 *
 * @author tomoaki
 */
class ManagerFactory implements FactoryInterface
{
    protected $managerClass = 'Flower\View\Pane\PaneManager';

    protected $builderClass = 'Flower\View\Pane\Builder\Builder';

    protected $builderDefaultPaneClass = 'Flower\View\Pane\PaneClass\Pane';

    protected $configKey = 'flower_pane_manager';

    /**
     * @param  ServiceLocatorInterface $serviceLocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof HelperPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }
        $config = $serviceLocator->get('Config');
        if (isset($config[$this->configKey])) {
            $config = $config[$this->configKey];
        } else {
            $config = array();
        }

        if (isset($config['manager_class'])) {
            $this->managerClass = $config['manager_class'];
        }

        $manager = new $this->managerClass;

        if (isset($config['pane_config'])) {
            $manager->setConfig($config['pane_config']);
        }

        if (isset($config['builder_options'])) {
            $bOptions = $config['builder_options'];
            if (isset($bOptions['builder_class'])) {
                $this->builderClass = $bOptions['builder_class'];
                unset ($bOptions['builder_class']);
            }
            if (!isset($bOptions['pane_class'])) {
                $bOptions['pane_class'] = $this->builderDefaultPaneClass;
            }
            $builder = new $this->builderClass($bOptions);
            $manager->setBuilder($builder);
        }

        if (isset($config['renderer_class'])) {
            $manager->setRendererClass($config['renderer_class']);
        }

        if (isset($config['listener_aggregates'])) {
            $eventManager = $manager->getEventManager();
            foreach ((array) $config['listener_aggregates'] as $listenerAggregate) {
                if (is_string($listenerAggregate)) {
                    if ($serviceLocator->has($listenerAggregate)) {
                        $listenerAggregate = $serviceLocator->get($listenerAggregate);
                    } elseif (is_subclass_of($listenerAggregate, 'Zend\EventManager\ListenerAggregateInterface', true)) {
                        $listenerAggregate = new $listenerAggregate;
                    } else {
                        throw new RuntimeException($listenerAggregate . ' is not service name nor ListenerAggregateInterface');
                    }
                }

                if ($listenerAggregate instanceof ListenerAggregateInterface) {
                    $eventManager->attachAggregate($listenerAggregate);
                } else {
                    if (is_object($listenerAggregate)) {
                        $listenerAggregate = get_class($listenerAggregate);
                    } elseif( !is_string($listenerAggregate)) {
                        $listenerAggregate = gettype($listenerAggregate);
                    }
                    throw new \Exception('invalid listenerAggregate:' . $listenerAggregate);
                }
            }
        }

        return $manager;
    }
}
