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
use Flower\View\Pane\PaneClass\SharedPaneInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\View\Helper\AbstractHelper;
use Zend\Stdlib\ArrayUtils;

/**
 * Flower\View\Pane\PaneClass\Paneツリーを管理
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class PaneManager extends AbstractHelper implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;

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

    protected $refreshEvents = array(
        PaneEvent::EVENT_REFRESH_CONFIG => PaneEvent::EVENT_REFRESH_CONFIG,
        PaneEvent::EVENT_REFRESH_PANE => PaneEvent::EVENT_REFRESH_PANE,
        PaneEvent::EVENT_REFRESH_RENDER => PaneEvent::EVENT_REFRESH_RENDER,
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

    /**
     *
     * @var string $paneId
     */
    public function get($paneId)
    {
        if (! is_string($paneId)) {
            throw new RuntimeException('PaneManager::get only accept string param');
        }
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

        $res = $events->trigger($getEvent);
        if ($getEvent->propagationIsStopped()) {
            $pane = $res->last();
        } else {
            $pane = $getEvent->getResult();
        }

        if ($pane instanceof SharedPaneInterface) {
            $registry->$paneId = $pane;
        }

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

        $res = $events->trigger($loadEvent);
        if ($loadEvent->propagationIsStopped()) {
            //終了条件を指定した場合は、リスナー側がresultをセットしない場合があり、
            //trigger側で終了を宣言する場合がある。
            $config = $res->last();
        } else {
            $config = $loadEvent->getResult();
        }

        $buildEvent = new PaneEvent(PaneEvent::EVENT_BUILD_PANE);
        $buildEvent->setManager($this);
        $buildEvent->setPaneId($paneId);
        $buildEvent->setTarget($paneId);
        $buildEvent->setParams($config);

        $res = $events->trigger($buildEvent);
        if ($buildEvent->propagationIsStopped()) {
            $pane = $res->last();
        } else {
            $pane = $buildEvent->getResult();
        }

        $pane->setPaneId($paneId);
        return $pane;
    }

    public function render($paneId, $options = array())
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
        $renderEvent = new PaneEvent(PaneEvent::EVENT_RENDER);
        $renderEvent->setManager($this);
        $renderEvent->setPaneId($paneId);
        $renderEvent->setParams($options);

        $res = $events->trigger($renderEvent);
        if ($renderEvent->propagationIsStopped()) {
            //終了条件を指定した場合は、リスナー側がresultをセットしない場合があり、
            //trigger側で終了を宣言する場合がある。
            $rendered = $res->last();
        } else {
            $rendered = $renderEvent->getResult();
        }
        return $rendered;

    }

    public function renderPane(PaneInterface $pane, $options = array())
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
        $renderEvent = new PaneEvent(PaneEvent::EVENT_RENDER);
        $renderEvent->setManager($this);
        $renderEvent->setPaneId($pane->getPaneId());//設定されていない場合がある。
        $renderEvent->setParams($options);
        $renderEvent->setTarget($pane);
        $res = $events->trigger($renderEvent);

        if ($renderEvent->propagationIsStopped()) {
            //終了条件を指定した場合は、リスナー側がresultをセットしない場合があり、
            //trigger側で終了を宣言する場合がある。
            $rendered = $res->last();
        } else {
            $rendered = $renderEvent->getResult();
        }

        return $rendered;
    }

    public function refresh($paneId, $types = null)
    {
        if (null === $types) {
            $types = $this->refreshEvents;
        }

        $events = $this->getEventManager();

        $results = array();
        foreach ((array) $types as $type) {
            if (!isset($this->refreshEvents[$type])) {
                continue;
            }
            $refreshEvent = new PaneEvent($type);
            $refreshEvent->setManager($this);
            $refreshEvent->setPaneId($paneId);
            $refreshEvent->setTarget($this);

            $results[] = $events->trigger($refreshEvent);
        }

        return $results;
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

    public function onRender(PaneEvent $e)
    {
        if ($e->getTarget() instanceof PaneInterface) {
            return;
        }

        $paneId = $e->getPaneId();

        $pane = $this->get($paneId);
        if (! $pane instanceof PaneInterface) {
            return '';
        }

        if (!$pane->getPaneId()) {
            $pane->setPaneId($paneId);
        }

        $e->setTarget($pane);
    }

    public function onGet(PaneEvent $e)
    {
        $pane = $this->build($e->getPaneId());

        $pre = $e->getResult();

        if ($pre instanceof PaneInterface) {
            $pre->insert($pane, $pane->getOrder());
            return $pre;
        } else {
            $e->setResult($pane);
            return $pane;
        }
    }

    public function onLoadConfig(PaneEvent $e)
    {
        $pre = $e->getResult();
        $config = $this->getPaneConfig($e->getPaneId());
        if (is_array($pre)) {
            /**
             * ２階層目以下で値を上書きすることはできない。
             * 基本的に１階層目はプロパティの変更が可能
             * ２階層目以降は数値添字になるのでエントリーを追加する形になります。
             */
            $config = ArrayUtils::merge($pre, $config);
        }
        $e->setResult($config);
        return $config;
    }

    public function onRenderPane(PaneEvent $e)
    {
        $pane = $e->getTarget();
        $options = $e->getParams();
        $renderer = $this->createPaneRenderer($pane, $options);

        ob_start();
        foreach ($renderer as $entry) {}
        $rendered = ob_get_clean();

        $e->setResult($rendered);

        return $rendered;
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
        //paneId to Pane
        $events->attach(PaneEvent::EVENT_RENDER, array($this, 'onRender'), 100);
        //Pane to string
        $events->attach(PaneEvent::EVENT_RENDER, array($this, 'onRenderPane'));
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
