<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane;

use RecursiveIteratorIterator;
use Closure;

class PaneRenderer extends RecursiveIteratorIterator
{
    protected $vars;

    protected $view;

    protected $_indent = "  ";

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
        echo "\n<!-- begin PaneRenderer -->\n";
        echo parent::getInnerIterator()->wrapBegin($this->getDepth());
    }

    public function endIteration()
    {
        echo parent::getInnerIterator()->wrapEnd($this->getDepth());
        echo "\n<!-- end PaneRenderer -->\n";
    }

    public function beginChildren()
    {
        $depth = $this->getDepth();
        $indent = str_repeat($this->_indent, $depth);
        echo $indent . parent::getInnerIterator()->wrapBegin($depth);
        $this->endTagStack[] = $indent . parent::getInnerIterator()->wrapEnd($depth);

    }

    public function endChildren()
    {
        echo array_pop($this->endTagStack);
    }

    public function current()
    {
        if (!parent::valid()) {
            return;
        }

        $depth = $this->getDepth();
        $indent = str_repeat($this->_indent, $depth + 1);
        $innerIndent = $indent . $this->_indent;

        if (!isset(parent::current()->var) || (!$var = parent::current()->var)) {
            echo $indent . parent::current()->begin($depth);
            echo $innerIndent . "<!-- var is omitted -->\n";
            echo $indent . parent::current()->end($depth);
            return;
        }

        if (is_string($var)) {
            $var_comment = htmlspecialchars($var);
            echo $indent . "<!-- start content $var_comment -->\n";
            echo $indent . parent::current()->begin($depth);
            if (isset($this->vars->$var)) {
                echo $this->vars->$var;
                echo PHP_EOL;
            }
            else {
                echo $innerIndent . "<!-- var $var_comment is not found -->\n";
            }
            echo $indent . parent::current()->end($depth);
            echo "\n" . $indent . "<!-- end content $var_comment -->\n";
        }
        elseif ($var instanceof Closure) {
            echo $indent . "<!-- start content Closure -->\n";
            echo $indent . parent::current()->begin($depth);
            echo $var($this);
            echo $indent . parent::current()->end($depth);
            echo $indent . "\n<!-- end content Closure -->\n";
        }
        elseif (is_callable($var)) {
            echo $indent . "<!-- start content Callable -->\n";
            echo $indent . parent::current()->begin($depth);
            echo $var($this);
            echo $indent . parent::current()->end($depth);
            echo $indent . "\n<!-- end content Callable -->\n";
        }
    }

    public function __toString()
    {
        ob_start();
        foreach($this as $entry) {}
        return ob_get_clean();
    }
}