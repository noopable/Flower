<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane;

use Zend\View\View;

/**
 * Description of Anchor
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class Anchor extends ListPane
{
    public $wrapTag = 'li';

    public $tag = 'a';

    protected $defaultSubstituteTag = 'span';

    public $href;

    protected $route;

    public function __construct()
    {
        parent::__construct();
        //default renderring policy => callback
        $this->var = array($this, 'render');
    }

    public function begin($depth = null)
    {
        //beginの判定をいつするかか。
        $this->setView($paneRenderer->getView());
        if ($this->tag == 'a') {
            //hrefの割り当てを試す
            $href = $this->getHref();
            if (!isset($href)) {
                $this->tag = $this->getSubstituteTag();
            }
            $this->attributes['href'] = $href;
        }
    }

    public function getSubstituteTag()
    {
        $substitute = $this->getOption('substitute_tag');
        if (empty($substitute)) {
            $substitute = $this->defaultSubstituteTag;
        }
        return $substitute;
    }

    public function setView(View $view)
    {
        $this->view = $view;
    }

    public function getView()
    {
        return $this->view;
    }

    public function setHref($href)
    {
        $this->href = $href;
    }

    public function getHref()
    {
        if (isset($this->href)) {
            return $this->href;
        }

        if ($href = $this->getOption('href')) {
            $this->href = $href;
            return $href;
        }
        // @todo optionからrouteを元に作成する
    }

    public function render(PaneRenderer $paneRenderer)
    {
        $this->setView($paneRenderer->getView());
        switch ($this->getOption('render_policy')) {
            case 'view_partial':
                $script = $this->getOption('render_script');
                echo $this->getView()->render($script);
                return;
            case 'built_in':
            default:
                if ($label = $this->getOption('label')) {
                    echo $this->getView()->escapeHtml($label);
                } else {
                    echo $this->getView()->escapeHtml($this->getHref());
                }
                return;
        }
    }

}
