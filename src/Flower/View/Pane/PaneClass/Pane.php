<?php

/**
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\PaneClass;

use Flower\View\Pane\RuntimeException;
use Flower\RecursivePriorityQueue;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 *
 */
class Pane extends RecursivePriorityQueue implements SharedPaneInterface
{
    use PaneTrait;

    protected static $factoryClass = 'Flower\View\Pane\Factory\PaneFactory';

    /**
     *
     * @var Callable
     */
    protected $sizeToClassFunction;

    public function __construct()
    {
        parent::__construct(RecursivePriorityQueue::HAS_CHILDREN_STRICT_CONTAINS);
        $this->tag = 'div';
        $this->var = 'content';
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