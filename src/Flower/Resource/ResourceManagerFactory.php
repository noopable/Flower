<?php
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\Resource;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Flower\Resource\Exception\RuntimeException;
use Flower\Resource\Manager\Config;
use Flower\Resource\Manager\ManagerInterface;


/**
 * 
 *
 * @author tomoaki
 */
class ResourceManagerFactory implements FactoryInterface {
    
    protected $configId = 'flower_resource_manager';
    /**
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Flower\Resource\Manager\ManagerInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config')[$this->configId];
        
        if (isset($config['di'])) {
            $di = new \Zend\Di\Di;
            $diConfig =new \Zend\Di\Config($config['di']);
            $diConfig->configure($di);
            try {
                //alias get
                $service = $di->get('manager');
            } catch (\Zend\Di\Exception\ExceptionInterface $e) {
                if (!isset($config['throw_exception']) || $config['throw_exception'] !== false) {
                    throw new RuntimeException('invalid configuration of resource manager of di', 0, $e);
                }
            }
        } 
        
        if (!isset($service) || ! $service instanceof ManagerInterface) {
            $class = isset($config['class']) ? $config['class'] : 'Flower\Resource\Manager\StandardManager';
            $service = new $class;
        }
        
        $config['service_locator'] = $serviceLocator;
        $managerConfig = new Config($config);
        $managerConfig->configure($service);

        return $service;
    }
}
