<?php

namespace Flower\Model;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use ArrayObject;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 *
 */
abstract class AbstractEntity extends ArrayObject
{
    /**
     * column aliases
     * key = material name
     * value = logical name
     * @var array
     */
    protected $columns = array();
    /**
     *
     * @param array $array
     */
    public function __construct(array $array = array())
    {
        parent::__construct($array, ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     *  $identifier keys to identify this entity
     *
     * @return array
     */
    abstract public function getIdentifier();

    /**
     * convert material name to logical name
     * if not in_set columns return material name
     * @param type $key
     * @return type
     */
    public function column($key)
    {
        if (isset($this->columns[$key])) {
            return $this->columns[$key];
        }
        return $key;
    }

    public function offsetGet($name)
    {
        if (! $this->offsetExists($name)) {
            return null;
        }
        return parent::offsetGet($name);
    }
    
}