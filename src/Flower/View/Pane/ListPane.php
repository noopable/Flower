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

    public $containerTag = 'ul';

    public $wrapTag = 'li';

    public $tag = 'span';

    /**
     *  var をcallbackとして使うため
     *　render時に、実質的なvarとして働く
     *
     * @var type
     */
    public $_var;

    protected $_indent = "  ";

    public $containerBegin;

    public $containerEnd;

    public function containerBegin($depth = null)
    {
        if ($depth === 0) {
            return $this->containerBegin;
        }
        $indent = str_repeat($this->_indent, $depth);
        return $this->wrapBegin .
                $indent . $this->begin($depth) .
                $this->render($this->getPaneRenderer()) .
                $indent . $this->end($depth) . "\n" .
                $indent . $this->containerBegin;
    }

    public function containerEnd($depth = null)
    {
        if ($depth === 0) {
            return $this->containerEnd;
        }
        $indent = str_repeat($this->_indent, $depth);
        return $this->containerEnd . "\n" . $indent . $this->wrapEnd;
    }

    public function wrapBegin($depth = null)
    {
        return $this->wrapBegin;
    }

    public function wrapEnd($depth = null)
    {
        return $this->wrapEnd;
    }

    public function render(PaneRenderer $paneRenderer)
    {
        $depth = $paneRenderer->getDepth();
        $indent = str_repeat($this->_indent, $depth + 1);
        $innerIndent = $indent . $this->_indent;
        $response = '';

        $var = $this->_var;
        if (is_string($var)) {
            $var_comment = htmlspecialchars($var);
            $response .= $indent . "<!-- start content $var_comment -->\n";
            if (isset($paneRenderer->getVars()->$var)) {
                $response .= $paneRenderer->getVars()->$var;
                $response .= "\n";
            } else {
                $response .= $innerIndent . "<!-- var $var_comment is not found -->\n";
            }
            $response .= $indent . "<!-- end content $var_comment -->\n";
        } elseif ($var instanceof Closure) {
            $response .= $indent . "<!-- start content Closure -->\n";
            $response .= $var($paneRenderer);
            $response .= "\n";
            $response .= $indent . "<!-- end content Closure -->\n";
        }

        return $response;
    }
}
