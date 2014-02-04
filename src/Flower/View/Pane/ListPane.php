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

    protected $containerEndStack = array();

    public function containerBegin($depth = null)
    {
        $renderSelf = false;
        $hasChildren = false;
        $response = '';
        $containerEnd = '';
        $indent = str_repeat($this->indent, (int) $depth);

        if ($depth === 0) {
            //第１階層はulでラップする。
            $response = $indent . $this->containerBegin;
            $containerEnd .= $indent . $this->containerEnd;
        }

        if ($this->hasContent()) {
            $renderSelf = true;
            //自要素を表示する
            if (strlen($response)) {
                $response .= $this->linefeed . $indent;
            }
            $response .= $this->wrapBegin . $this->linefeed . //<li>
                $indent . $this->begin($depth) . $this->linefeed . //<span>
                $this->render($this->getPaneRenderer())  . //content
                $indent . $this->end($depth); //</span>
        }
        if ($this->valid()) {
            $hasChildren = true;
            //子要素をcontainerでラップする
            $response .=  $this->linefeed .
                    $indent . $this->containerBegin; //<ul>
            if ($renderSelf) {
                //自要素のラップを後で閉じる
                if (empty($containerEnd)) {
                    $containerEnd =
                        $this->containerEnd . $this->linefeed . //</ul>
                        $indent . $this->wrapEnd;
                } else {
                    $containerEnd =
                        $this->containerEnd . $this->linefeed . //</ul>
                        $indent . $this->wrapEnd . $this->linefeed .//</li>
                        $indent . $containerEnd;//</ul>
                }
            }
        } elseif ($renderSelf) {
            //自要素のラップをすぐに閉じる
            $response .=  $this->linefeed . $indent . $this->wrapEnd; //</li>
        } else {
            //子要素も自要素も表示しないなら何も表示しない
            $this->containerEndStack[] = '';
            return '';
        }

        $this->containerEndStack[] = $containerEnd;
        return $response;
    }

    public function containerEnd($depth = null)
    {
        return array_pop($this->containerEndStack);
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

    public function hasContent()
    {
        //empty() is true when '0'
        return !empty($this->_var) || '0' === $this->_var;
    }
}
