<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Service;

use Zend\Cache\Storage\StorageInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of ConfigFileListenerFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class RenderCacheListenerFactory implements FactoryInterface
{
    protected $configKey = 'render_cache_listener';

    protected $listenerClass = 'Flower\View\Pane\Service\RenderCacheListener';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if (!$serviceLocator->has('Config')) {
            return;
        }

        $config = $serviceLocator->get('Config');

        if (!isset($config[$this->configKey])) {
            echo "cache not founce";
            return;
        }

        $listener = new $this->listenerClass;

        if (! isset($config[$this->configKey]['cache_storage'])) {
            echo "options not found";
            return;
        }

        $cacheStorage = $config[$this->configKey]['cache_storage'];

        if (is_array($cacheStorage)) {
            $listener->setStorageOptions($cacheStorage);
        } elseif (is_object($cacheStorage) && $cacheStorage instanceof StorageInterface) {
            $listener->setStorage($cacheStorage);
        }

        return $listener;
    }

}
