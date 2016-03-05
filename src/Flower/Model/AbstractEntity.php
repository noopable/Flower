<?php
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Model;

use ArrayObject;
use Zend\Stdlib\ArrayUtils;

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

    protected $maskFields = array();

    protected $replaceWithExchange = false;

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

    public function populate($data, $columnCheck = true)
    {
        foreach ($data as $key => $val) {
            if ($columnCheck) {
                if (!isset($this->columns[$key])) {
                    continue;
                }
            }
            $this->offsetSet($key, $val);
        }
    }

    public function offsetGet($name)
    {
        if (! $this->offsetExists($name)) {
            return null;
        }
        return parent::offsetGet($name);
    }

    public function getArrayCopy($safe = false)
    {
        $array = parent::getArrayCopy();
        if ($safe && count($this->maskFields)) {
            return array_diff_key($array, array_flip($this->maskFields));
        }
        return $array;
    }

    public function exchangeArray($data, $resetColumns = null)
    {
        if (!($this->replaceWithExchange || $resetColumns)) {
            $data = array_merge($this->getArrayCopy(), $data);
        }
        return parent::exchangeArray($data);
    }
}