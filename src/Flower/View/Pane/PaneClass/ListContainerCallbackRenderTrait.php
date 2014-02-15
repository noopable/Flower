<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\PaneClass;

use Flower\View\Pane\PaneRenderer;

/**
 * Description of ListContainerBeginTrait
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
trait ListContainerCallbackRenderTrait
{
    use CallbackRenderTrait;
    
    protected $containerEndStack = array();

    public function containerBegin($depth = null)
    {
        $renderSelf = false;
        $response = '';
        $containerEnd = '';
        $indent = str_repeat($this->indent, (int) $depth);
        $renderer = $this->getPaneRenderer();
        if ($renderer instanceof PaneRenderer) {
            $maxDepth = $renderer->getMaxDepth();
        } else {
            $maxDepth = false;
        }

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
                $this->_render($renderer) . $this->linefeed; //content
        }
        if ($this->valid() && (($maxDepth === false) || ($depth < $maxDepth))) {
            //子要素をcontainerでラップする
            $response .= $indent . $this->containerBegin; //<ul>
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
            $response .=  $indent . $this->wrapEnd; //</li>
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
}
