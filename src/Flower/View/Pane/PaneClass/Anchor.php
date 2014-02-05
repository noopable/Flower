<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\PaneClass;

use Flower\View\Pane\PaneRenderer;
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
     * @see Flower\View\Pane\PaneClass\Pane::var
     * @var type
     */
    public $var = '';

    public $href;

    public $route;

    public $controller;

    public $action;

    public $params = array();

    protected $view;

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

        $route = $this->route;
        $params = $this->params;
        if (isset($this->controller)) {
            $params['controller'] = $this->controller;
        }

        if (isset($this->action)) {
            $params['action'] = $this->action;
        }

        if (empty($route) && empty($params)) {
            return false;
        }
        if (empty($params)) {
            $params = array();
        }

        $options = $this->getOption('route_options');
        if (empty($options)) {
            $options = array();
        }

        $reuseMatched = (bool) $this->getOption('reuse_matched_params');

        $this->href = $this->getHrefWithViewHelper($route, $params, $options, $reuseMatched);

        return $this->href;
    }

    public function _render(PaneRenderer $paneRenderer)
    {
        $depth = $paneRenderer->getDepth();
        $this->indent = $paneRenderer->indent;
        $indent = str_repeat($this->indent, $depth + 1);
        $this->linefeed = $paneRenderer->linefeed;
        $this->commentEnable = $paneRenderer->commentEnable;

        $response = '';
        $response .= $indent . $this->begin($depth);
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
                $response .= $view->render($this->_var);
                break;
            case 'raw':
                $response .=  (string) $this->_var;
                break;
            case 'callback':
                if (is_callable($this->_var)) {
                    $response .= call_user_func($this->_var, $paneRenderer);
                    break;
                } else {
                    //__toString() must not throw an exception
                    return 'render_policy is callback. but var is not callable. in ' . __METHOD__;
                }
            case 'default':
            default:
                if (isset($this->label)) {
                    $response .= $view->escapeHtml($this->label);
                    break;
                } else {
                    if (! is_string($this->_var)) {
                        return 'label is not set and var is not string';
                    }
                    $response .= $view->escapeHtml($this->_var);
                    break;
                }
        }

        $response .= $this->end($depth);
        return $response;
    }

    public function hasContent()
    {
        //empty() is true when '0'
        return !empty($this->_var) || '0' === $this->_var || !empty($this->label) || '0' === $this->label;
    }

    /**
     * @see Zend\View\Helper\Url
     *
     * Generates an url given the name of a route.
     *
     * @see    Zend\Mvc\Router\RouteInterface::assemble()
     * @param  string               $route               Name of the route
     * @param  array                $params             Parameters for the link
     * @param  array|Traversable    $options            Options for the route
     * @param  bool                 $reuseMatched Whether to reuse matched parameters
     * @return string Url                         For the link href attribute
     * @throws Exception\RuntimeException         If no RouteStackInterface was provided
     * @throws Exception\RuntimeException         If no RouteMatch was provided
     * @throws Exception\RuntimeException         If RouteMatch didn't contain a matched route name
     * @throws Exception\InvalidArgumentException If the params object was not an array or \Traversable object
     */
    public function getHrefWithViewHelper($route = null, $params = array(), $options = array(), $reuseMatched = false)
    {
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

        try {
            $urlHelper = $view->plugin('url');
            $this->href = $urlHelper($route, $params, $options, $reuseMatched);
        } catch (\Exception $ex) {
            //__toString() must not throw an exception
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
}
