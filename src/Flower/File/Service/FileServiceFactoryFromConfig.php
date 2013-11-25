<?php
namespace Flower\File\Service;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of FireServiceFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class FileServiceFactoryFromConfig implements FactoryInterface  {
    
    protected $configKey = 'flower_file';
    
    protected $specConfig = 'Flower\File\Service\SpecConfig';
    
    
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        
        $config = $serviceLocator->get('Config')[$this->configKey];
        
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
            throw new \RuntimeException('configurator not instantiable in ' . __CLASS__);
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
