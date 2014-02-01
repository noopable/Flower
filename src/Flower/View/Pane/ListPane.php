<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane;

use Flower\View\Pane\PaneRenderer;

/**
 * Description of ListPane
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ListPane extends Pane
{
    protected static $factoryClass = 'Flower\View\Pane\ListPaneFactory';

    public $wrapTag = 'ul';

    public $tag = 'li';

    /**
     *  var をcallbackとして使うため
     *　render時に、実質的なvarとして働く
     *
     * @var type
     */
    public $_var;

    protected $_indent = "  ";

    public function wrapBegin($depth = null)
    {
        if ($depth === 0) {
            return $this->wrapBegin;
        }

        $indent = str_repeat($this->_indent, $depth);
        return $this->begin($depth) .
                $this->render($this->getPaneRenderer()) . 
                $indent . $this->wrapBegin;
    }

    public function wrapEnd($depth = null)
    {
        if ($depth === 0) {
            return $this->wrapEnd;
        }
        $indent = str_repeat($this->_indent, $depth);
        return $this->wrapEnd . PHP_EOL . $indent . $this->end($depth) . PHP_EOL;
    }

    public function render(PaneRenderer $paneRenderer)
    {
        $depth = $paneRenderer->getDepth();
        $indent = str_repeat($this->_indent, $depth + 1);
        $innerIndent = $indent . $this->_indent;

        $var = $this->_var;
        if (is_string($var)) {
            $var_comment = htmlspecialchars($var);
            echo $indent . "<!-- start content $var_comment -->\n";
            if (isset($paneRenderer->getVars()->$var)) {
                echo $paneRenderer->getVars()->$var;
                echo PHP_EOL;
            } else {
                echo $innerIndent . "<!-- var $var_comment is not found -->\n";
            }
            echo "\n" . $indent . "<!-- end content $var_comment -->\n";
        } elseif ($var instanceof Closure) {
            echo $indent . "<!-- start content Closure -->\n";
            echo $var($paneRenderer);
            echo "\n" . $indent . "<!-- end content Closure -->\n";
        }
    }
}
