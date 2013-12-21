<?php
namespace Flower\View\Pane;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
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

}