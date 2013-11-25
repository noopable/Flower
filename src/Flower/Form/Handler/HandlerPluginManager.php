<?php
namespace Flower\Form\Handler;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\ServiceManager\AbstractPluginManager;
/**
 * Description of HandlerPluginManager
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class HandlerPluginManager extends AbstractPluginManager
{
    protected $namespaces = array();

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
    public function get($name, $options = array(), $usePeeringServiceManagers = true)
    {
        if (!$this->has($name)) {
            throw \RuntimeException('Cannot get ' . $name . ' in ' . get_class($this));
        }

        return parent::get($name, $options, $usePeeringServiceManagers);
    }

    /**
     * @param  string|array  $name
     * @param  bool          $checkAbstractFactories
     * @param  bool          $usePeeringServiceManagers
     * @return bool
     */
    public function has($name, $checkAbstractFactories = true, $usePeeringServiceManagers = true)
    {
        $has = parent::has($name, $checkAbstractFactories, $usePeeringServiceManagers);
        if ($has) {
            return true;
        }

        if ($this->autoAddInvokableClass) {
            foreach ($this->namespaces as $namespace) {
                $class = $namespace . '\\' . strtr(ucfirst(strtr($name, '-_', ' ')), ' ', '');
                if (class_exists($class)) {
                    $this->setInvokableClass($name, $class);
                    return true;
                }
            }
        }
        return false;
    }

    public function setNamespaces($namespaces)
    {
        $this->namespaces = $namespaces;
    }

    public function addNamespace($namespace)
    {
        $this->namespaces[] = $namespace;
    }
    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws \RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof FormHandlerInterface) {
            return;
        }

        throw new \RuntimeException('plugin doesn\'t implement FormHandlerInterface');
    }
}
