<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Service;

use Flower\View\Pane\Exception\RuntimeException;
use Zend\Cache\Storage\StorageInterface;
use Zend\Cache\StorageFactory;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Description of AbstractLazyLoadCacheListener
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
abstract class AbstractLazyLoadCacheListener extends AbstractCacheListener implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $cacheServiceName;

    public function setCacheServiceName($cacheServiceName)
    {
        $this->cacheServiceName = $cacheServiceName;
    }

    public function getCacheServiceName()
    {
        return $this->cacheServiceName;
    }

    public function setStorage(StorageInterface $storage = null)
    {
        if (null === $storage) {
            do {
                $cacheServiceName = $this->getCacheServiceName();
                $serviceLocator = $this->getServiceLocator();

                if (!isset($cacheServiceName)) {
                    break;
                }

                if (!isset($serviceLocator)) {
                    $message = 'LazyLoadCacheListener needs ServiceLocator';
                    break;
                }

                if (! $serviceLocator->has($cacheServiceName)) {
                    $message = 'specified servicename ' . $cacheServiceName . ' is not found';
                    break;
                }

                $storage = $serviceLocator->get($cacheServiceName);
            } while(false);

            if (!isset($storage) && ($storageOptions = $this->getStorageOptions())) {
                try {
                    $storage = StorageFactory::factory($storageOptions);
                } catch (InvalidArgumentException $ex) {
                    throw new RuntimeException('try to make a cache storage, but invalid options', $ex->getCode(), $ex);
                }
            } elseif (isset($message)) {
                throw new RuntimeException($message);
            }
        }

        $this->storage = $storage;
    }
}
