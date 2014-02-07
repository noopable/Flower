<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane;

use ArrayObject;
use Flower\View\Pane\Builder\Builder;
use Flower\View\Pane\Exception\RuntimeException;
use Flower\View\Pane\PaneClass\PaneInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\ProvidesEvents;
use Zend\View\Helper\AbstractHelper;
use Zend\Stdlib\ArrayUtils;

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

    protected $builder = 'Flower\View\Pane\Builder\Builder';

    protected $config = array();

    protected $registry;

    protected $defaultListenersWait = true;

    protected $view;

    protected $init = false;

    protected $modePaneClass = array(
        'pane' => 'Flower\View\Pane\PaneClass\Pane',
        'list' => 'Flower\View\Pane\PaneClass\ListPane',
        'anchor' => 'Flower\View\Pane\PaneClass\Anchor',
    );

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
        $this->init();

        $registry = $this->getRegistry();

        if (isset($registry->$paneId)) {
            return $registry->$paneId;
        }

        $events = $this->getEventManager();

        /**
         * Constructor
         *
         * Accept a target and its parameters.
         *
         * @param  string $name Event name
         * @param  string|object $target
         * @param  array|ArrayAccess $params
         */
        $getEvent = new PaneEvent(PaneEvent::EVENT_GET_PANE);
        $getEvent->setManager($this);
        $getEvent->setPaneId($paneId);
        $getEvent->setTarget($paneId);

        $pane = $events->trigger($getEvent)->last();

        $registry->$paneId = $pane;

        return $pane;
    }

    public function build($paneId)
    {
        $this->init();

        $events = $this->getEventManager();
        /**
         * Constructor
         *
         * Accept a target and its parameters.
         *
         * @param  string $name Event name
         * @param  string|object $target
         * @param  array|ArrayAccess $params
         */
        $loadEvent = new PaneEvent(PaneEvent::EVENT_LOAD_CONFIG);
        $loadEvent->setManager($this);
        $loadEvent->setPaneId($paneId);
        $loadEvent->setTarget($paneId);

        $config = $events->trigger($loadEvent)->last();

        $buildEvent = new PaneEvent(PaneEvent::EVENT_BUILD_PANE);
        $buildEvent->setManager($this);
        $buildEvent->setPaneId($paneId);
        $buildEvent->setTarget($paneId);
        $buildEvent->setParams($config);

        return $events->trigger($buildEvent)->last();
    }

    public function render($paneId, $options = null)
    {
        //キャッシュマネージャーを入れてキャッシュから取り出せるようにする。
        $pane = $this->get($paneId);
        $renderer = $this->createPaneRenderer($pane, $options);
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

    public function onGet(PaneEvent $e)
    {
        $pane = $this->build($e->getPaneId());

        $pre = $e->getTarget();

        if ($pre instanceof PaneInterface) {
            $pre->insert($pane, $pane->getOrder());
            $e->setTarget($pre);
            return $pre;
        } else {
            return $pane;
        }
    }

    public function onLoadConfig(PaneEvent $e)
    {
        $pre = $e->getTarget();
        $config = $this->getPaneConfig($e->getPaneId());
        if (is_array($pre)) {
            /**
             * ２階層目以下で値を上書きすることはできない。
             * 基本的に１階層目はプロパティの変更が可能
             * ２階層目以降は数値添字になるのでエントリーを追加する形になります。
             */
            $config = ArrayUtils::merge($pre, $config);
        }
        $e->setTarget($config);
        return $config;
    }

    /**
     * MvcではサービスファクトリでサービスでBuilderOptionを取得してから
     * Builderを設定する
     *
     * @param \Flower\View\Pane\Builder\Builder $builder
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

    public function setBuilderMode($mode)
    {
        $mode = strtolower($mode);
        if (isset($this->modePaneClass[$mode])) {
            $this->getBuilder()->setPaneClass($this->modePaneClass[$mode]);
        }
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
        $events->attach(PaneEvent::EVENT_GET_PANE, array($this, 'onGet'));
        $events->attach(PaneEvent::EVENT_BUILD_PANE, array($this->getBuilder(), 'onBuild'));
        $events->attach(PaneEvent::EVENT_LOAD_CONFIG, array($this, 'onLoadConfig'));
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

    public function createPaneRenderer(PaneInterface $pane, $options = null)
    {
        $renderer = new $this->rendererClass($pane);
        if (isset($options)) {
            $rendererConfig = new RendererConfig($options);
            $rendererConfig->configure($renderer);
        }

        if (!$view = $this->getView()) {
            throw new RuntimeException('Set PhpRenderer or use me via ViewHelper');
        }
        $renderer->setVars($view->vars());
        $renderer->setView($view);
        return $renderer;
    }
}
