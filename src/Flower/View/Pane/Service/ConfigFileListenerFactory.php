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

        if (!isset($config[$this->configKey])
                || !isset($config[$this->configKey]['file_service'])) {
            return;
        }

        $fileServiceName = $config[$this->configKey]['file_service'];

        if (! $serviceLocator->has($fileServiceName)) {
            throw new RuntimeException('specified service ' . $fileServiceName . ' is not found');
        }

        $fileService = $serviceLocator->get($fileServiceName);

        if (! $fileService instanceof GatewayInterface) {
            throw new RuntimeException('failed to load file service');
        }

        $listener = new $this->listenerClass;

        $listener->setFileService($fileService);

        return $listener;
    }

}
