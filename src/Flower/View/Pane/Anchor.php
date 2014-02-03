<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane;

//use Flower\View\Pane\Exception\RuntimeException;
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

    /**
     * reset var as default innerHtml value
     *
     * @see Flower\View\Pane\Pane::var
     * @var type
     */
    public $var = '';

    public $href;

    public $route;

    public $controller;

    public $action;

    public $params = array();

    protected $view;

    public function begin($depth = null)
    {
        if ($href = $this->getHref()) {
            //hrefの割り当てを試す
            $tag = $this->tag;
            $this->attributes['href'] = $href;
        } else {
            $tag = $this->getSubstituteTag();
        }
        $attributeString = AnchorPaneFactory::attributesToAttributeString($this->attributes);
        if (strlen($attributeString)) {
            $this->begin = sprintf('<%s%s>', $tag, $attributeString);
        } else {
            $this->begin = '<' . $tag . '>';
        }

        return $this->begin;
    }

    public function end($depth = null)
    {
        if ($this->getHref()) {
            //hrefの割り当てを試す
            $tag = $this->tag;
        } else {
            $tag = $this->getSubstituteTag();
        }
        return '</' . $tag . '>';
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
                return 'PaneRenderer is not set' . __METHOD__;
            }
            $paneRenderer = $this->getPaneRenderer();
            $view = $paneRenderer->getView();
            if (!$view instanceof View) {
                return 'PhpRenderer not found. Normally you may have it from helper.' . __METHOD__;
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

        if (isset($this->view)) {
            $view = $this->view;
        } else {
            $view = $paneRenderer->getView();
            if ($view instanceof View) {
                $this->setView($view);
            } else {
                return 'PhpRenderer not found. Normally you may have it from helper.:' . __METHOD__;
                //__toString() must not throw an exception
                //throw new RuntimeException('PhpRenderer not found. Normally you may have it from helper.');
            }
        }

        switch ($this->getOption('render_policy')) {
            case 'view_partial':
                return $view->render($this->_var);
            case 'raw':
                return (string) $this->_var;
            case 'callback':
                if (is_callable($this->_var)) {
                    return call_user_func($this->_var, $paneRenderer);
                } else {
                    //__toString() must not throw an exception
                    return 'render_policy is callback. but var is not callable. in ' . __METHOD__;
                }
            case 'default':
            default:
                if (isset($this->label)) {
                    return $view->escapeHtml($this->label);
                } else {
                    if (is_string($this->_var)) {
                        return $view->escapeHtml($this->_var);
                    }
                    return 'label is not set and var is not string';
                }
        }
    }

}
