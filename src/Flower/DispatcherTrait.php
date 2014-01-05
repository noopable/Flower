<?php
namespace Flower;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\View\Model\ViewModel;

use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Description of ViewModelFromDispatch
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
trait DispatcherTrait {
    use ServiceLocatorAwareTrait;
    
    protected $controller;
    
    protected $dispatchOptions;
    
    protected $signature;
    
    public function setControllerName($controllerName)
    {
        $this->controllerName = $controllerName;
    }
    
    public function setDispatchOptions($options)
    {
        $this->dispatchOptions = $options;
    }
    
    public function setSignature($signature)
    {
        $this->signature = $signature;
    }
    
    protected function dispatch()
    {
        if (!isset($this->serviceLocator)) {
            throw new Exception\RuntimeException('ServiceLocator is required in the Dispatch Process');
        }
        
        if (!isset($this->controllerName)) {
            throw new Exception\RuntimeException('ControllerName is required in the Dispatch Process');
        }
        
        $pluginManager = $this->serviceLocator->get('ControllerPluginManager');
        $forwarder = $pluginManager->get('forward');
        return $forwarder->dispatch($this->controllerName, $this->dispatchOptions);
    }
    
}
