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

    public $containerBegin;

    public $containerEnd;

    public function containerBegin($depth = null)
    {
        if ($depth === 0) {
            return $this->containerBegin;
        }
        $indent = str_repeat($this->indent, $depth);
        return $this->wrapBegin . $this->linefeed .
                $indent . $this->begin($depth) . $this->linefeed .
                $this->render($this->getPaneRenderer()) . $this->linefeed .
                $indent . $this->end($depth) . $this->linefeed .
                $indent . $this->containerBegin;
    }

    public function containerEnd($depth = null)
    {
        if ($depth === 0) {
            return $this->containerEnd;
        }
        $indent = str_repeat($this->indent, $depth);
        return $this->containerEnd . $this->linefeed . $indent . $this->wrapEnd;
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
        $this->indent = $paneRenderer->indent;
        $this->linefeed = $paneRenderer->linefeed;
        $this->commentEnable = $paneRenderer->commentEnable;

        $depth = $paneRenderer->getDepth();
        $indent = str_repeat($this->indent, $depth + 1);
        $innerIndent = $indent . $this->indent;
        $response = '';

        $var = $this->_var;
        if (is_string($var)) {
            $var_comment = htmlspecialchars($var);
            $response .= $this->commentEnable ? $indent . "<!-- start content $var_comment -->" . $this->linefeed : "";
            if (isset($paneRenderer->getVars()->$var)) {
                $response .= $paneRenderer->getVars()->$var . $this->linefeed;
            } else {
                $response .= $this->commentEnable ? $innerIndent . "<!-- var $var_comment is not found -->" . $this->linefeed : "";
            }
            $response .= $this->commentEnable ? $indent . "<!-- end content $var_comment -->" . $this->linefeed : "";
        } elseif ($var instanceof Closure) {
            $response .= $this->commentEnable ? $indent . "<!-- start content Closure -->" . $this->linefeed : "";
            $response .= $var($paneRenderer) . $this->linefeed;
            $response .= $this->commentEnable ? $indent . "<!-- end content Closure -->" . $this->linefeed : "";
        }

        return $response;
    }
}
