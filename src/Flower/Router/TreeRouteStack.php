<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Router;

use Flower\Router\LazyLoadResourceRouteFactory;
use Flower\Router\LazyLoadFileRouteFactory;
use Zend\Mvc\Router\Http\TreeRouteStack as ZfRouteStack;
use Zend\ServiceManager\ServiceLocatorInterface;
/**
 * Description of TreeRouteStack
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class TreeRouteStack extends ZfRouteStack{

    protected $fileServiceName = 'Flower_LazyRouteLoaderFile';

    protected $resourceServiceName = 'Flower_LazyRouteLoaderResource';

    public function getPrototypes()
    {
        return $this->prototypes;
    }

    public function init()
    {
        parent::init();
        $routePlugins = $this->routePluginManager;
        $serviceLocator = $routePlugins->getServiceLocator();

        if ($serviceLocator instanceof ServiceLocatorInterface ) {
            if ($serviceLocator->has($this->fileServiceName)) {
                $factory = new LazyLoadFileRouteFactory;
                $fileService = $serviceLocator->get($this->fileServiceName);
                $factory->setFileService($fileService);
                $routePlugins->setFactory('lazyFile', $factory);
            }

            if ($serviceLocator->has($this->resourceServiceName)) {
                $factory = new LazyLoadResourceRouteFactory;
                $resourceManager = $serviceLocator->get($this->resourceServiceName);
                $factory->setResourceManager($resourceManager);
                $routePlugins->setFactory('lazyResource', $factory);
            }
        }

    }

    public function setFileServiceName($fileServiceName)
    {
        $this->fileServiceName = $fileServiceName;
    }

    public function getFileServiceName()
    {
        return $this->fileServiceName;
    }

    public function setResourceServiceName($resourceServiceName)
    {
        $this->resourceServiceName = $resourceServiceName;
    }

    public function getResourceServiceName()
    {
        return $this->resourceServiceName;
    }
}
