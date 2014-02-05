<?php

/**
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\PaneClass;

use ArrayObject;
use Flower\View\Pane\PaneRenderer;
use Flower\View\Pane\RuntimeException;
use Flower\RecursivePriorityQueue;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 *
 */
class Pane extends RecursivePriorityQueue implements PaneInterface
{
    protected static $factoryClass = 'Flower\View\Pane\Factory\PaneFactory';

    protected $paneId;

    public $id;

    public $name;

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

    public $label;

    public $var = 'content';

    public $tag = 'div';

    public $wrapTag;

    public $containerTag;

    protected $options;

    protected $paneRenderer;

    protected $containerBegin;

    protected $containerEnd;

    protected $wrapBegin;

    protected $wrapEnd;

    public $begin;

    public $end;


    protected $registry;
    /**
     *
     * @var Callable
     */
    protected $sizeToClassFunction;

    public $indent = "  ";

    public $linefeed = "\n";

    public $commentEnable = true;

    public function __construct()
    {
        parent::__construct(RecursivePriorityQueue::HAS_CHILDREN_STRICT_CONTAINS);
    }

    public function setPaneId($paneId)
    {
        $this->paneId = $paneId;
    }

    public function getPaneId()
    {
        return $this->paneId;
    }

    public function getOrder()
    {
        return $this->order;
    }

    /**
     *
     * @param PaneInterface $pane
     * @param type $priority
     * @return type
     */
    public function insert($pane, $priority = null)
    {
        if (! $pane instanceof PaneInterface) {
            throw new RuntimeException('Pane accept object only PaneInterface');
        }
        if (null === $priority) {
            $priority = $pane->getOrder();
        }

        if ($paneId = $pane->getPaneId()) {
            $registry = $this->getRegistry();
            if (!isset($registry->$paneId)) {
                $registry->$paneId = $pane;
            }
        }

        return parent::insert($pane, $priority);
    }

    public function containerBegin($depth = null)
    {
        if (!isset($this->containerBegin)) {
            return $this->wrapBegin($depth);
        }
        return $this->containerBegin;
    }

    public function containerEnd($depth = null)
    {
        if (!isset($this->containerEnd)) {
            return $this->wrapEnd($depth);
        }
        return $this->containerEnd;
    }

    public function wrapBegin($depth = null)
    {
        if (! isset($this->wrapBegin)) {
            return $this->begin($depth);
        }
        return $this->wrapBegin;
    }

    public function wrapEnd($depth = null)
    {
        if (! isset($this->wrapEnd)) {
            return $this->end($depth);
        }
        return $this->wrapEnd;
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
        $this->indent = $paneRenderer->indent;
        $this->linefeed = $paneRenderer->linefeed;
        $this->commentEnable = $paneRenderer->commentEnable;
        $this->paneRenderer = $paneRenderer;
    }

    public function getPaneRenderer()
    {
        return $this->paneRenderer;
    }

    public function hasContent()
    {
        //empty() is true when '0'
        return !empty($this->var) || '0' === $this->var;
    }

    public function setRegistry($registry)
    {
        $this->registry = $registry;
    }

    public function getRegistry()
    {
        if (!isset($this->registry)) {
            $this->registry = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        }
        return $this->registry;
    }

    public function setSizeToClassFunction($sizeToClassFunction)
    {
        if (!is_callable($sizeToClassFunction)) {
            throw new RuntimeException('sizeToClassFunction should be callable');
        }

        $this->sizeToClassFunction = $sizeToClassFunction;
    }

    public function getSizeToClassFunction()
    {
        return $this->sizeToClassFunction;
    }
    /**
     * for util
     * pane size to class
     *
     * @param mixed $size
     * @return string $class
     */
    public function sizeToClass($size = 0)
    {
        if (isset($this->sizeToClassFunction) && is_callable($this->sizeToClassFunction)) {
            $class = call_user_func($this->sizeToClassFunction, $size);
        } else {
            //default for twitter bootsrap 2
            // convert to small decimal string
            $class = 'span' . (string) (intval($size) % 36);
        }
        return $class;
    }
}