<?php
namespace Flower\Model\Service;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
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
     * whether or not use DependencyInjector
     * 
     * @var bool
     */
    protected $useDi = true;
    
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if (class_exists($this->managerClass)) {
            $class = $this->managerClass;
        }
        else {
            trigger_error('class not found:' . $this->managerClass, E_USER_WARNING);
            $class = 'Flower\Model\Service\RepositoryPluginManager';
        }
        
        $config = $serviceLocator->get('Config');
        
        
        if (isset($config[$this->configId])) {
            $managerConfig = $config[$this->configId];
            $managerConfig['service_locator'] = $serviceLocator;
        }
        else {
            $managerConfig = array('service_locator' => $serviceLocator);
        }
        
        $service = new $class(new RepositoryPluginConfig($managerConfig));
        
        $service->addPeeringServiceManager($serviceLocator);
        
        return $service;
    }
}
