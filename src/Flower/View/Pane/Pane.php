<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane;

use Flower\RecursivePriorityQueue;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 *
 */
class Pane extends RecursivePriorityQueue implements PaneInterface
{
    public $id;

    /**
     *
     * @var array
     */
    public $classes;

    /**
     *
     * @var array
     */
    public $attributes;

    public $order = 1;

    public $size;

    public $var = 'content';

    public $begin;

    public $end;

    public $tag = 'div';

    protected $options;

    public function __construct(Pane $parent = null)
    {
        if (null !== $parent) {
            $this->parent = $parent;
        }
        parent::__construct(RecursivePriorityQueue::HAS_CHILDREN_STRICT_CONTAINS);
    }

    public function getOrder()
    {
        return $this->order;
    }

    /**
     *
     * @param type $value
     * @param type $priority
     * @return type
     */
    public function insert($value, $priority = null)
    {
        if (null === $priority) {
            if (is_object($value) && method_exists($value, 'getOrder')) {
                $priority = $value->getOrder();
            }
        }

        return parent::insert($value, $priority);
    }

    public function begin()
    {
        return $this->begin;
    }

    public function end()
    {
        return $this->end;
    }

    public function setBegin($begin)
    {
        $this->begin = $begin;
    }

    public function setEnd($end)
    {
        $this->end = $end;
    }

    public function getOption($name)
    {
        if (! isset($this->options[$name])) {
            return;
        }
        return $this->options[$name];
    }

    public function getOptions()
    {
        if (!isset($this->options)) {
            return array();
        }
        return $this->options;
    }

    public function setOption($name, $option)
    {
        if (!isset($this->options)) {
            $this->options = array();
        }
        $this->options[$name] = $option;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

}