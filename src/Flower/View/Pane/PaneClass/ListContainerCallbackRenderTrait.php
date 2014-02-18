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
        $renderSelf = $this->hasContent();
        $response = '';
        $containerEnd = '';
        $indent = str_repeat($this->indent, (int) $depth);
        $renderer = $this->getPaneRenderer();
        if ($renderer instanceof PaneRenderer) {
            $maxDepth = $renderer->getMaxDepth();
        } else {
            $maxDepth = false;
        }

        if ($renderSelf ) {
            if ($depth === 0) {
                //第１階層で自要素があるなら、ulでオーバーラップする。
                $response = $indent . $this->containerBegin;
                $containerEnd .= $indent . $this->containerEnd;
            }
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
                        $containerEnd;//</ul>
                }
            } else {
                //子要素はあるが、自要素がない場合、ラップせずにコンテナを後で閉じる
                if (empty($containerEnd)) {
                    $containerEnd = $indent . $this->containerEnd;
                } else {
                    $containerEnd = $indent . $this->containerEnd . $this->linefeed . $containerEnd;
                }
            }
        } elseif ($renderSelf) {
            //子要素がないが、自要素がある場合、ラップをすぐに閉じる
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
