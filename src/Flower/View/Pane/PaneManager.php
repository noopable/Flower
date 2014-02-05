<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane;

use ArrayObject;
use Flower\View\Pane\Exception\RuntimeException;
use Flower\View\Pane\PaneClass\PaneInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\ProvidesEvents;
use Zend\View\Helper\AbstractHelper;

/**
 * Flower\View\Pane\PaneClass\Paneツリーを管理
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class PaneManager extends AbstractHelper implements EventManagerAwareInterface
{
    use ProvidesEvents;

    protected $eventIdentifier = 'PaneManager';

    protected $rendererClass = 'Flower\View\Pane\PaneRenderer';

    protected $builder = 'Flower\View\Pane\Builder';

    protected $config = array();

    protected $registry;

    protected $defaultListenersWait = true;

    protected $view;

    protected $init = false;

    public function __invoke()
    {
        $this->init();
        return $this;
    }

    public function init()
    {
        if ($this->init) {
            return true;
        }

        $this->init = true;

        if ($this->defaultListenersWait) {
            $this->attachDefaultListers();
        }
    }

    public function get($paneId)
    {
        $registry = $this->getRegistry();

        if (isset($registry->$paneId)) {
            return $registry->$paneId;
        }
        $pane = $this->build($paneId);

        $registry->$paneId = $pane;

        return $pane;
    }

    public function build($paneId)
    {
        $this->init();
        $event = new PaneEvent(PaneEvent::EVENT_BUILD_PANE);
        $event->setManager($this);
        $event->setPaneId($paneId);
        $event->setTarget($paneId);
        $event->setParams($this->getConfig($paneId));

        $events = $this->getEventManager();
        $res = $events->trigger($event);

        return $res->last();

    }

    public function render($paneId)
    {
        //キャッシュマネージャーを入れてキャッシュから取り出せるようにする。
        $pane = $this->get($paneId);
        $renderer = $this->createPaneRenderer($pane);
        return $renderer->__toString();
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setPaneConfig($paneId, $config)
    {
        $this->config[$paneId] = $config;
    }

    public function getPaneConfig($paneId, array $default = array())
    {
        if (! isset($this->config[$paneId])) {
            return $default;
        }
        return $this->config[$paneId];
    }

    /**
     * MvcではサービスファクトリでサービスでBuilderOptionを取得してから
     * Builderを設定する
     *
     * @param \Flower\View\Pane\Builder $builder
     */
    public function setBuilder(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function getBuilder()
    {
        if (!isset($this->builder)) {
            return;
        }

        if (is_string($this->builder)) {
            $this->builder = new $this->builder;
        }

        return $this->builder;
    }

    public function setRendererClass($rendererClass)
    {
        $this->rendererClass = $rendererClass;
    }

    public function getRendererClass()
    {
        return $this->rendererClass;
    }

    public function attachDefaultListers()
    {
        $events = $this->getEventManager();
        $events->attach(PaneEvent::EVENT_BUILD_PANE, $this->getBuilder());

        $this->defaultListenersWait = false;
    }

    public function setRegistry($registry)
    {
        $this->registry = $registry;
    }

    public function getRegistry()
    {
        if (!isset($this->registry)) {
            $this->registry = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        }
        return $this->registry;
    }

    public function createPaneRenderer(PaneInterface $pane)
    {
        $renderer = new $this->rendererClass($pane);
        if (!$view = $this->getView()) {
            throw RuntimeException('Set PhpRenderer or use me via ViewHelper');
        }
        $renderer->setVars($view->vars());
        $renderer->setView($view);
        return $renderer;
    }
}
