<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane;

use Flower\View\Pane\PaneClass\CallbackRenderInterface;
use Flower\View\Pane\PaneClass\PaneInterface;
use RecursiveIteratorIterator;
use Closure;

/**
 * beginIteration:
 *    全体のレンダリング開始時に一度だけ呼ばれる。
 * beginChildren:
 *    子RecursiveIteratorを開始するときに呼ばれる。
 * nextElement:
 *    各要素の開始前に呼ばれる。dividerを設定したいときなど
 *
 *
 */
class PaneRenderer extends RecursiveIteratorIterator
{
    protected $vars;

    protected $view;

    public $indent = "  ";

    public $linefeed = "\n";

    public $commentEnable = true;

    protected $endTagStack = array();


    public function __construct(PaneInterface $pane,
            $mode = RecursiveIteratorIterator::LEAVES_ONLY,
            $flag = RecursiveIteratorIterator::CATCH_GET_CHILD)
    {
        parent::__construct($pane, $mode, $flag);
    }

    public function setVars($vars)
    {
        //$varsはArrayだったらArrayObjectに変換する？
        $this->vars = $vars;
    }

    public function getVars()
    {
        return $this->vars;
    }

    public function setVar($name, $value)
    {
        if ($this->vars instanceof ArrayObject) {
            $this->vars->$name = $value;
        }
        elseif (is_array($this->vars) || $this->vars instanceof \ArrayAccess) {
            //$this->varsってarrayじゃ動かないんじゃない？
            $this->vars[$name] = $value;
        }
        elseif (!isset($this->vars)) {
            $this->vars = new \ArrayObject(array($name => $value), \ArrayObject::ARRAY_AS_PROPS);
        }

        return $this;
    }

    public function setView($view)
    {
        $this->view = $view;
    }

    public function getView()
    {
        return $this->view;
    }

    public function beginIteration()
    {
        parent::getInnerIterator()->setPaneRenderer($this);
        $this->commentEnable and print("<!-- begin Renderer -->" . $this->linefeed);
        echo parent::getInnerIterator()->containerBegin($this->getDepth()) . $this->linefeed;
    }

    public function endIteration()
    {
        echo parent::getInnerIterator()->containerEnd($this->getDepth()) . $this->linefeed;
        $this->commentEnable and print( "<!-- end Renderer -->" . $this->linefeed);
    }

    public function beginChildren()
    {
        $depth = $this->getDepth();
        $indent = str_repeat($this->indent, $depth);
        $pane = parent::getInnerIterator();
        $pane->setPaneRenderer($this);
        echo $indent . $pane->containerBegin($depth) . $this->linefeed;
        $this->endTagStack[] = $indent . $pane->containerEnd($depth);
    }

    public function endChildren()
    {
        echo array_pop($this->endTagStack) . $this->linefeed;
    }

    public function current()
    {
        if (!parent::valid()) {
            return;
        }

        $pane = parent::current();
        if (!$pane->hasContent()) {
            return;
        }

        if ($pane instanceof CallbackRenderInterface ){
            echo $pane->render($this);
            return;
        }

        $depth = $this->getDepth();
        $indent = str_repeat($this->indent, $depth + 1);
        $innerIndent = $indent . $this->indent;
        $var = $pane->var;

        if (is_string($var)) {
            $var_comment = htmlspecialchars($var);
            $this->commentEnable and print($indent . "<!-- start content $var_comment -->" . $this->linefeed);
            echo $indent . $pane->begin($depth) . $this->linefeed;
            if (isset($this->vars->$var)) {
                echo $this->vars->$var . $this->linefeed;
            } else {
                $this->commentEnable and print($innerIndent . "<!-- var $var_comment is not found -->" . $this->linefeed);
            }
            echo $indent . $pane->end($depth) . $this->linefeed;
            $this->commentEnable and print($indent . "<!-- end content $var_comment -->" . $this->linefeed);
        } elseif ($var instanceof Closure) {
            $this->commentEnable and print($indent . "<!-- start content Closure -->" . $this->linefeed);
            echo $indent . $pane->begin($depth);
            echo $var($this) . $this->linefeed;
            echo $indent . $pane->end($depth) . $this->linefeed;
            $this->commentEnable and print($indent . "<!-- end content Closure -->" . $this->linefeed);
        } elseif (is_callable($var)) {
            $this->commentEnable and print($indent . "<!-- start content Callable -->" . $this->linefeed);
            echo $indent . $pane->begin($depth);
            echo $var($this) . $this->linefeed;
            echo $indent . $pane->end($depth) . $this->linefeed;
            $this->commentEnable and print($indent . "<!-- end content Callable -->" . $this->linefeed);
        }
        return $pane;
    }

    public function __toString()
    {
        ob_start();
        //__toString() must not throw an exception
        try {
            foreach($this as $entry) {}
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }
        return ob_get_clean();
    }
}