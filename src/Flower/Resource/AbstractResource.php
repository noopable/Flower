<?php

namespace Flower\Resource;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\Stdlib\ArrayUtils;
/**

 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 *
 */
abstract class AbstractResource implements ResourceInterface
{
    public $name = '';

    public $class;

    static public $_nameDelimiter = '::';

    protected $options = array();

    public function getResourceId()
    {
        if (strlen($name)) {
            return $this->getResourceClass() & self::_nameDelimiter & $this->getName();
        }
        else {
            return $this->getResourceClass();
        }

    }

    public function setName($name)
    {
        $this->name = (string) $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setResourceClass($class)
    {
        $this->class = (string) $class;
    }

    public function getResourceClass()
    {
        if (!isset($this->class)) {
            $this->class = get_class($this);
        }

        return $this->class;
    }

    public function setPrototype($prototype)
    {
        $this->prototype = clone $prototype;
        $this->onInjectPrototype();
    }

    public function onInjectPrototype(){}

    /**
     * Set a single option
     *
     * @param  string $name
     * @param  mixed $value
     * @return ViewModel
     */
    public function setOption($name, $value)
    {
        $this->options[(string) $name] = $value;
        return $this;
    }

    /**
     * Get a single option
     *
     * @param  string       $name           The option to get.
     * @param  mixed|null   $default        (optional) A default value if the option is not yet set.
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        $name = (string)$name;
        if (! array_key_exists($name, $this->options)) {
            if (isset($this->prototype) && $this->prototype instanceof AbstractResource) {
                return $this->prototype->getOption($name, $default);
            }
            else {
                return $default;
            }
        }
        return $this->options[$name];
    }

    /**
     * Set renderer options/hints en masse
     *
     * @param array|\Traversable $options
     * @throws \Zend\View\Exception\InvalidArgumentException
     * @return self
     */
    public function setOptions($options, $clear = false)
    {
        // Assumption is that lowest common denominator for renderer configuration
        // is an array
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: expects an array, or Traversable argument; received "%s"',
                __METHOD__,
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }
        if ($clear) {
            $this->options = $options;
        }
        else {
            $this->options = ArrayUtils::merge($this->options, $options);
        }

        return $this;
    }

    /**
     * Set renderer options/hints en masse
     * ただし、２層までの継承ってかっこわるいかなという気はする。
     *
     * @param array|\Traversable $defaultOptions
     * @throws \Zend\View\Exception\InvalidArgumentException
     * @return self
     */
    public function setDefaultOptions($defaultOptions)
    {
        // Assumption is that lowest common denominator for renderer configuration
        // is an array
        if ($defaultOptions instanceof Traversable) {
            $defaultOptions = ArrayUtils::iteratorToArray($defaultOptions);
        }

        if (!is_array($defaultOptions)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: expects an array, or Traversable argument; received "%s"',
                __METHOD__,
                (is_object($odefaultOptions) ? get_class($defaultOptions) : gettype($defaultOptions))
            ));
        }

        $this->options = ArrayUtils::merge($defaultOptions, $this->options);
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Merge two arrays together.
     * Evenif an iteger key exists in both arrays, force overwrite it.
     *
     * not (If an integer key exists in both arrays, the value from the second array
     * will be appended the the first array.) If both values are arrays, they
     * are merged together, else the value of the second array overwrites the
     * one of the first array.
     *
     * @param  array $a
     * @param  array $b
     * @return array
     */
    protected function merge(array $a, array $b)
    {
        foreach ($b as $key => $value) {
            if (array_key_exists($key, $a)) {
                //if (is_int($key)) {
                //    $a[] = $value;
                //} else
                if (is_array($value) && is_array($a[$key])) {
                    $a[$key] = $this->merge($a[$key], $value);
                } else {
                    $a[$key] = $value;
                }
            } else {
                $a[$key] = $value;
            }
        }

        return $a;
    }
}