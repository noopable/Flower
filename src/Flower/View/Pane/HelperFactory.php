<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;

/**
 * Description of HelperFactory
 *
 * @author tomoaki
 */
class HelperFactory implements FactoryInterface
{

    protected $configKey = 'flower_pane';

    protected $helperClass = 'Flower\View\Pane\PaneHelper';

    protected $builderClass = 'Flower\View\Pane\Builder';
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

        if (isset($config['helper_class'])) {
            $this->helperClass = $config['helper_class'];
        }

        $helper = new $this->helperClass;

        if (isset($config['builder_options'])) {
            $bOptions = $config['builder_options'];
            if (isset($bOptions['builder_class'])) {
                $this->builderClass = $bOptions['builder_class'];
                unset ($bOptions['builder_class']);
            }
            if (!isset($bOptions['pane_class']) && isset($helper->defaultPaneClass)) {
                $bOptions['pane_class'] = $helper->defaultPaneClass;
            }
            $builder = new $this->builderClass($bOptions);
            $helper->setBuilder($builder);
        }

        return $helper;

    }

}
