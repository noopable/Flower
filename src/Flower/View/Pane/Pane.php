<?php

/**
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
    protected static $factoryClass = 'Flower\View\Pane\PaneFactory';

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

    public $wrapTag;

    public $tag = 'div';

    protected $options;

    protected $paneRenderer;

    public function __construct()
    {
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

    public function wrapBegin($depth = null)
    {
        if (isset($this->wrapBegin)) {
            return $this->wrapBegin;
        }
        return $this->begin;
    }

    public function wrapEnd($depth = null)
    {
        if (isset($this->wrapEnd)) {
            return $this->wrapEnd;
        }
        return $this->end;
    }

    public function begin($depth = null)
    {
        return $this->begin;
    }

    public function end($depth = null)
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

    public static function getFactoryClass()
    {
        return static::$factoryClass;
    }

    public function setWrapBegin($wrapBegin)
    {
        $this->wrapBegin = $wrapBegin;
    }

    public function setWrapEnd($wrapEnd)
    {
        $this->wrapEnd = $wrapEnd;
    }

    public function setPaneRenderer(PaneRenderer $paneRenderer)
    {
        $this->paneRenderer = $paneRenderer;
    }

    public function getPaneRenderer()
    {
        return $this->paneRenderer;
    }
}