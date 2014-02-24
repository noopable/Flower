<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\EventManager\Event;

use Flower\EventManager\Exception\RuntimeException;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of EventAbstractFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class EventAbstractFactory implements AbstractFactoryInterface, MutableCreationOptionsInterface
{
    protected $classes;

    /**
     * @see \Zend\ServiceManager\ServiceManager::$canonicalNamesReplacements
     * @var array map of characters to be replaced through strtr
     */
    protected $canonicalNamesReplacements = array('-' => '', '_' => '', ' ' => '', '\\' => '', '/' => '');

    protected $creationOptions = array();

    public function __construct()
    {
        $classes = array(
            'Zend\EventManager\Event',
            'Flower\File\Event',
            'Flower\View\Pane\PaneEvent',
        );

        $replacements = $this->canonicalNamesReplacements;
        $callback = function ($name) use ($replacements) {
            return strtolower(strtr($name, $replacements));
        };

        $keys = array_map($callback, $classes);

        $this->classes = array_combine($keys, $classes);
    }

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (! isset($this->classes[$name])) {
            return false;
        }
        return true;
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (!isset($this->classes[$name])) {
            throw new RuntimeException('AbstractFactory can\'t create ' . $requestedName);
        }

        $instance = new $this->classes[$name];
        foreach ($this->creationOptions as $key => $value) {
            switch ($key) {
                case "name":
                    $instance->setName($value);
                    break;
                case "params":
                    $instance->setParams($value);
                    break;
                case "target":
                    $instance->setTarget($value);
                    break;
                default:
                    $method = 'set' . lcfirst($key);
                    if (method_exists($instance, $method)) {
                        $instance->$method($value);
                    } else {
                        continue 2;
                    }
                    break;
            }
            unset($this->creationOptions[$key]);
        }
        if (!empty($this->creationOptions)) {
            $params = ArrayUtils::merge($this->creationOptions, $instance->getParams());
            $instance->setParams($params);
        }

        $this->creationOptions = array();

        return $instance;
    }

    public function setCreationOptions(array $options)
    {
        $this->creationOptions = $options;
    }

    public function addClass($class)
    {
        if (class_exists($class)) {
            $this->classes[strtolower(strtr($class, $this->canonicalNamesReplacements))] = $class;
        }
    }
}
