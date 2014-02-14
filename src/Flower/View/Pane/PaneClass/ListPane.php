<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\PaneClass;

use Flower\View\Pane\PaneRenderer;

/**
 * Description of ListPane
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ListPane extends Pane implements CallbackRenderInterface
{
    use ListContainerBeginEndTrait;

    protected static $factoryClass = 'Flower\View\Pane\Factory\ListPaneFactory';

    /**
     *  var をcallbackとして使うため
     *　render時に、実質的なvarとして働く
     *
     * @var type
     */
    public $_var;

    protected $containerEndStack = array();

    public function __construct()
    {
        parent::__construct();
        $this->containerTag = 'ul';
        $this->wrapTag = 'li';
        $this->tag = 'span';
    }

    public function render(PaneRenderer $paneRenderer)
    {
        $this->indent = $paneRenderer->indent;
        $this->linefeed = $paneRenderer->linefeed;
        $this->commentEnable = $paneRenderer->commentEnable;

        $depth = $paneRenderer->getDepth();
        $indent = str_repeat($this->indent, $depth + 1);

        $response = '';
        $response .= $this->commentEnable ? $indent . "<!-- start content CallbackRender -->" . $this->linefeed : '';
        $response .= $indent . $this->wrapBegin($depth) . $this->linefeed;
        $response .= $this->_render($paneRenderer) . $this->linefeed;
        $response .= $indent . $this->wrapEnd($depth) . $this->linefeed;
        $response .= $this->commentEnable ? $indent . "<!-- end content CallbackRender -->" . $this->linefeed : '';
        return $response;
    }

    public function _render(PaneRenderer $paneRenderer)
    {
        $this->indent = $paneRenderer->indent;
        $this->linefeed = $paneRenderer->linefeed;
        $this->commentEnable = $paneRenderer->commentEnable;

        $depth = $paneRenderer->getDepth();
        $indent = str_repeat($this->indent, $depth + 1);
        $innerIndent = $indent . $this->indent;
        $response = '';

        $response .= $indent . $this->begin($depth) . $this->linefeed;

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

        $response .= $indent . $this->end($depth);

        return $response;
    }

    public function hasContent()
    {
        //empty() is true when '0'
        return !empty($this->_var) || '0' === $this->_var;
    }
}
