<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\Model\Service;

use Flower\Model\Exception\RuntimeException;
use Zend\ServiceManager\Di\DiAbstractServiceFactory;
use Zend\ServiceManager\Di\DiServiceInitializer;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of ServiceFactory
 *
 */
class RepositoryServiceFactory implements FactoryInterface {

    /**
     *
     * @var string
     */
    protected $configId = 'flower_repositories';

    /**
     *
     * @var string
     */
    protected $managerClass = 'Flower\Model\Service\RepositoryPluginManager';

    /**
     * @param  ServiceLocatorInterface $serviceLocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if (class_exists($this->managerClass)) {
            $class = $this->managerClass;
        }
        else {
            throw new RuntimeException('repository plugin manager class not found:' . $this->managerClass);
        }

        if ($serviceLocator->has('Config')) {
            $config = $serviceLocator->get('Config');
        } else {
            $config = array();
        }
        
        if (isset($config[$this->configId])) {
            $managerConfig = $config[$this->configId];
            $managerConfig['service_locator'] = $serviceLocator;
        }
        else {
            $managerConfig = array('service_locator' => $serviceLocator);
        }

        $service = new $class(new RepositoryPluginConfig($managerConfig));

        $service->addPeeringServiceManager($serviceLocator);

        if ($serviceLocator->has('Di')) {
            $di = $serviceLocator->get('Di');
            $service->addAbstractFactory(
                new DiAbstractServiceFactory($di, DiAbstractServiceFactory::USE_SL_NONE)
            );
            $service->addInitializer(
                new DiServiceInitializer($di, $serviceLocator)
            );
        }

        return $service;
    }
}
