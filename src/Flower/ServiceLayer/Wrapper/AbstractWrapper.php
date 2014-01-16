<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\ServiceLayer\Wrapper;

use Flower\ServiceLayer\Exception\RuntimeException;
/**
 * Description of EventsWrapper
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
abstract class AbstractWrapper implements ServiceWrapperInterface {
    
    protected $proxyFactory;
    
    protected $wrapTargets;
    
    /**
     * 
     * @param \Flower\ServiceLayer\Wrapper\ProxyFactoryInterface $proxyFactory
     */
    public function setProxyFactory(ProxyFactoryInterface $proxyFactory)
    {
        $this->proxyFactory = $proxyFactory;
    }
    
    /**
     * 
     * @return \Flower\ServiceLayer\Wrapper\ProxyFactoryInterface 
     */
    public function getProxyFactory()
    {
        return $this->proxyFactory;
    }
    
    /**
     * 
     * @param array $wrapTargets
     * @return array
     */
    public function setWrapTargets(array $wrapTargets)
    {
        foreach ($wrapTargets as $target) {
            $this->addWrapTarget($target);
        }
        return $this->wrapTargets;
    }
    
    /**
     * 
     * @param string $name
     * @return array
     * @throws \Flower\ServiceLayer\Exception\RuntimeException
     */
    public function addWrapTarget($name)
    {
        if (is_object($name)) {
            $name = get_class($name);
        }
        
        if (!is_string($name)) {
            throw RuntimeException('set wrap target name');
        }
        $name = strtolower($name);
        
        $this->wrapTargets[$name] = $name;
        return $this->wrapTargets;
    }
    
    /**
     * 
     * @param string $name
     * @return array
     */
    public function removeWrapTarget($name)
    {
        $name = strtolower($name);
        if (isset($this->wrapTargets[$name])) {
            unset($this->wrapTargets[$name]);
        }
        return $this->wrapTargets;
    }
    
    /**
     * 
     * @return array
     */
    public function getWrapTargets()
    {
        return $this->wrapTargets;
    }
    
    /**
     * 
     * @param string $name
     * @return boolean
     */
    public function isWrapTarget($name)
    {
        $name = strtolower($name);
        return isset($this->wrapTargets[$name]);
    }
    
    /**
     * 
     * @param type $name
     * @param $instance
     * @return type
     */
    public function wrap($name,$instance)
    {
        if (!$this->isWrapTarget($name)) {
            return $instance;
        }
        
        $proxyFactory = $this->getProxyFactory();
        
        if (! $proxyFactory instanceof ProxyFactoryInterface) {
            return $instance;
        }
        
        return $proxyFactory->factory($instance);
    }
 
}
