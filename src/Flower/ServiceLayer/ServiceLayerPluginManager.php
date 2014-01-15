<?php

namespace Flower\ServiceLayer;

use Flower\ServiceLayer\Exception\RuntimeException;
use Zend\ServiceManager\AbstractPluginManager;

class ServiceLayerPluginManager extends AbstractPluginManager
{

    protected $config;
    
    protected $wrappers;
    
    /**
     * クラスを配置する namespace as prefix
     * 他の場所のクラスを使いたいときは、直接getで取得するか、
     * 同じnamespaceにプロキシを配置する。
     * 
     * @var string 
     */
    protected $pluginNameSpace;
    
    /**
     * Retrieve a service from the manager by name
     *
     * Allows passing an array of options to use when creating the instance.
     * createFromInvokable() will use these and pass them to the instance
     * constructor if not null and a non-empty array.
     *
     * @param  string $name
     * @param  array $options
     * @param  bool $usePeeringServiceManagers
     * @return object
     */
    public function get($name, $options = array(), $usePeeringServiceManagers = true, $underAccessControl = false)
    {
        if (isset($this->pluginNameSpace)) {
            // Allow specifying a class name directly; registers as an invokable class
            if (!$this->has($name) && $this->autoAddInvokableClass) {
                $this->autoAddInvokableClassByNamespace($name);
            }
        }

        try {
            $instance = parent::get($name, $options, $usePeeringServiceManagers);
        } catch (RuntimeException $ex) {
            $message = $ex->getMessage();
            throw new RuntimeException($name . ' of service is invalid :' . $message , 0, $ex);
        }
        
        if (isset($this->wrappers)) {
            foreach ($this->wrappers as $wrapper) {
                $instance = $wrapper->wrap($instance, $name);
            }
        }
        
        return $instance;
    }
    
    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof ServiceLayerInterface) {
            // we're okay
            return;
        }

        throw new RuntimeException(sprintf(
            'Service of type %s is invalid;Please implement %s\ServiceLayerInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
    
    public function addServiceWrapper($wrapper, $priority = 1)
    {
        //@todo priorityQueue?
        if (!isset($this->wrappers)) {
            //priorityQueue?
            $this->wrappers = array();
        }
        $this->wrappers[] = $wrapper;
    }

    public function setPluginNameSpace($pluginNameSpace)
    {
        $this->pluginNameSpace = (string) $pluginNameSpace;
    }
    
    public function getPluginNameSpace()
    {
        return $this->pluginNameSpace;
    }
    /**
     * 
     * @param string $name
     */
    public function autoAddInvokableClassByNamespace($name)
    {
        if (!isset($this->pluginNameSpace)) {
            return;
        }
        
        if (($pluginNameSpace = $this->getPluginNameSpace()) && (strpos($pluginNameSpace, $name) !== 0)) {
            $class = rtrim($pluginNameSpace, '\\') . '\\' . ucfirst($name);
            if (class_exists($class)) {
                $this->setInvokableClass($name, $class);
            }
        }
    }

}
