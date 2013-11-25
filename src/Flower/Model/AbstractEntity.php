<?php

namespace Flower\Model;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
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

    public function column($key)
    {
        if (isset($this->columns[$key])) {
            return $this->columns[$key];
        }
        return $key;
    }
}