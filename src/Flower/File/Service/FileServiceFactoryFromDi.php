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
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Description of FireServiceFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class FileServiceFactoryFromDi implements FactoryInterface  {
    
    protected $configKey = 'flower_file';
    
    protected $specClass = 'Flower\File\Spec\TreeArrayMerge';
    
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        
        $config = $serviceLocator->get('Config')[$this->configKey];
        
        if (! isset($config['spec_class'])) {
            $specClass = $this->specClass;
        }
        else {
            //you can use diAlias or instance
            $specClass = $config['spec_class'];
        }
        
        if (isset($config['spec_options'])) {
            //to retrieve specified instance
            $specConf = $config['spec_options'];
        }
        else {
            $specConf = array();
        }
        
        if (! $serviceLocator->has('Di')) {
            throw new \RuntimeException('Di not found in ServiceLocator');
        }
        
        $specConf['serviceLocator'] = $serviceLocator;
        $spec = $serviceLocator->get('Di')->get($specClass, $specConf);
        
        $spec->configure();
        
        return $spec->getGateway();
        
    }
    
}
