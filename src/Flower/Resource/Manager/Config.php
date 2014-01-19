<?php

/*
 * Here comes the text of your license
 * Each line should be prefixed with  * 
 */

namespace Flower\Resource\Manager;

use Flower\Resource\Converter\DefaultConverter;
use Flower\Resource\ResourcePluginManagerAwareInterface;
/**
 * Description of Config
 *
 * @author tomoaki
 */
class Config {
    protected $config;
    
    protected $resourcePluginManagerServiceName = 'Flower_Resources';
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    public function configure(ManagerInterface $manager)
    {
        if (isset($this->config['service_locator'])) {
            $serviceLocator = $this->config['service_locator'];
        }
        
        if (isset($this->config['cache_storage'])) {
            $traits = class_uses($manager);
            if (in_array('Flower\Resource\Manager\CacheStorageTrait', $traits)) {
                $storage = \Zend\Cache\StorageFactory::factory($this->config['cache_storage']);
                $manager->setStorage($storage);
            }
        }
        
        if (isset($this->config['converter'])) {
            $converter = new $this->config['converter'];
        } else {
            $converter = new DefaultConverter;
        }
        
        $manager->setConverter($converter);
        
        if (isset($serviceLocator) && isset($converter)) {
            //2階層以上のカスタマイズになると、DIが優位
            $resourcePluginService = isset($this->config['resource_plugin_manager'])
                                                ? $this->config['resource_plugin_manager']
                                                : $this->resourcePluginManagerServiceName;
            if ($serviceLocator->has($resourcePluginService)) {
                $resourcePluginManager = $serviceLocator->get($resourcePluginService);
                if ($manager instanceof ResourcePluginManagerAwareInterface) {
                    $manager->setResourcePluginManager($resourcePluginManager);
                }
                if ($converter instanceof ResourcePluginManagerAwareInterface) {
                    $converter->setResourcePluginManager($resourcePluginManager);
                }
            }
        }

        if (isset($this->config['mapper'])) {
            
        }
    }
}
