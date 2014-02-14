<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\PaneClass;

use Flower\View\Pane\Exception\RuntimeException;
use Flower\View\Pane\PaneRenderer;
use Zend\View\Exception\RuntimeException as ZendViewException;

/**
 * Description of ViewScriptPane
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ViewScriptPane extends ListPane
{
    protected static $factoryClass = 'Flower\View\Pane\Factory\ViewScriptPaneFactory';

    public function __construct()
    {
        parent::__construct();
        $this->containerTag = 'div';
        $this->wrapTag = 'div';
        $this->tag = 'div';
    }
    
    public function _render(PaneRenderer $paneRenderer)
    {
        $this->indent = $paneRenderer->indent;
        $this->linefeed = $paneRenderer->linefeed;
        $this->commentEnable = $paneRenderer->commentEnable;

        $depth = $paneRenderer->getDepth();
        $indent = str_repeat($this->indent, $depth + 1);
        $response = '';

        $response .= $indent . $this->begin($depth) . $this->linefeed;

        $var = $this->_var;
        if (is_string($var)) {
            $view = $paneRenderer->getView();
            if (!$view) {
                throw new RuntimeException('paneRenderer has no PhpRenderer');
            }
            $var_comment = htmlspecialchars($var);
            $response .= $this->commentEnable ? $indent . "<!-- start content $var_comment -->" . $this->linefeed : "";
            try {
                $response .= $view->render($var);
            } catch (ZendViewException $ex) {
                $response .= "<!-- error: " . $view->escapeHtml($ex->getMessage()) . " -->";
            }
            $response .= $this->commentEnable ? $indent . "<!-- end content $var_comment -->" . $this->linefeed : "";
        }

        $response .= $indent . $this->end($depth);

        return $response;
    }
}
