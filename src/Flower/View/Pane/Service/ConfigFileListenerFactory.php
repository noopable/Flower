<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Service;

use Flower\File\Gateway\GatewayInterface;
use Flower\View\Pane\Exception\RuntimeException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of ConfigFileListenerFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ConfigFileListenerFactory implements FactoryInterface
{
    protected $configKey = 'pane_config_file_listener';

    protected $listenerClass = 'Flower\View\Pane\Service\ConfigFileListener';

    protected $specConfig = 'Flower\File\Service\SpecConfig';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if (!$serviceLocator->has('Config')) {
            return;
        }

        $config = $serviceLocator->get('Config');

        if (!isset($config[$this->configKey])) {
            return;
        }

        $fileService = $this->getFileService($serviceLocator, $config[$this->configKey]);

        if (! $fileService instanceof GatewayInterface) {
            throw new RuntimeException('failed to load file service');
        }

        $listener = new $this->listenerClass;

        $listener->setFileService($fileService);

        return $listener;
    }

    public function getFileService($serviceLocator, array $config)
    {
        $config['serviceLocator'] = $serviceLocator;

        if (isset($config['configurator'])) {
            //you can use instance
            $configurator = $config['configurator'];
        }
        else {
            $configurator = $this->specConfig;
        }

        if (is_string($configurator) && class_exists($configurator)) {
            $configurator = new $configurator($config);
        }

        // TODO: create interface and check interface
        if (!is_object($configurator)) {
            throw new RuntimeException('configurator not instantiable in ' . __CLASS__);
        }

        /**
         *
         * instantiate core spec
         */
        $spec = $configurator->createSpec();

        /**
         *
         * inject dependencies with config and more outer work
         */
        $configurator->configure($spec);

        /**
         *
         * attachListenerAggregate and more inner work
         */
        $spec->configure();

        return $spec->getGateway();
    }

}
