<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane;

use Flower\View\Pane\Exception\RuntimeException;
use Zend\View\Renderer\PhpRenderer as View;

/**
 * Description of Anchor
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class Anchor extends ListPane
{
    protected static $factoryClass = 'Flower\View\Pane\AnchorPaneFactory';

    public $containerTag = 'ul';

    public $wrapTag = 'li';

    public $tag = 'a';

    protected $defaultSubstituteTag = 'span';

    public $href;

    protected $route;

    protected $view;

    public function __construct()
    {
        parent::__construct();
        //default renderring policy => callback
        $this->var = array($this, 'render');
    }

    public function begin($depth = null)
    {
        if ($this->tag == 'a') {
            //hrefの割り当てを試す
            $href = $this->getHref();
            if (!isset($href)) {
                $this->tag = $this->getSubstituteTag();
            }
            $this->attributes['href'] = $href;
        }
        $attributeString = AnchorPaneFactory::attributesToAttributeString($this->attributes);
        if (strlen($attributeString)) {
            $this->begin = sprintf('<%s%s>', $this->tag, $attributeString);
        } else {
            $this->begin = '<' . $this->tag . '>';
        }

        return $this->begin;
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

        $view = $this->getView();
        if (!$view instanceof View) {
            if (! isset($this->paneRenderer)) {
                //__toString() must not throw an exception
                //and if exceptions catched in upper layer, HTML structure will be broken
                return 'PaneRenderer is not set' . __LINE__;
            }
            $paneRenderer = $this->getPaneRenderer();
            $view = $paneRenderer->getView();
            if (!$view instanceof View) {
                return 'PhpRenderer not found. Normally you may have it from helper.' . __LINE__;
                //__toString() must not throw an exception
                //throw new RuntimeException('PhpRenderer not found. Normally you may have it from helper.');
            }
        }
        $urlHelper = $view->plugin('url');

        $route = $this->getOption('route');
        $params = $this->getOption('params');
        if (empty($params)) {
            $params = array();
        }
        $options = $this->getOption('route_options');
        if (empty($options)) {
            $options = array();
        }
        $reuseMatched = (bool) $this->getOption('reuse_matched_params');
        
        /**
         *
         * @see Zend\View\Helper\Url
         * @param  string               $name               Name of the route
         * @param  array                $params             Parameters for the link
         * @param  array|Traversable    $options            Options for the route
         * @param  bool                 $reuseMatchedParams Whether to reuse matched parameters
         */
        try {
            $this->href = $urlHelper($route, $params, $options, $reuseMatched);
        } catch (\Exception $ex) {
            /**
             * No RouteStackInterface instance provided
             *  Urlヘルパーにrouterがセットされていない場合
             * No RouteMatch instance provided
             *  RouteMatchが設定されていないがルート名が指定されていない場合
             * RouteMatch does not contain a matched route name
             *  RouteMatchにmatchedRouteNameが設定されていない場合
             *
             * ※いずれもMvcフローから呼ばれていない可能性が高い
             *
             */
            $this->href = $ex->getMessage();
        }
        return $this->href;

    }

    public function render(PaneRenderer $paneRenderer)
    {
        $this->indent = $paneRenderer->indent;
        $this->linefeed = $paneRenderer->linefeed;
        $this->commentEnable = $paneRenderer->commentEnable;

        $view = $paneRenderer->getView();
        if ($view instanceof View) {
            $this->setView($view);
        } else {
            return 'PhpRenderer not found. Normally you may have it from helper.:'  . __LINE__;
            //__toString() must not throw an exception
            //throw new RuntimeException('PhpRenderer not found. Normally you may have it from helper.');
        }

        switch ($this->getOption('render_policy')) {
            case 'view_partial':
                $script = $this->getOption('render_script');
                return $view->render($this->_var);
            case 'default':
            default:
                if ($label = $this->getOption('label')) {
                    return $view->escapeHtml($label);
                } else {
                    return $view->escapeHtml($this->getHref());
                }
        }
    }

}
