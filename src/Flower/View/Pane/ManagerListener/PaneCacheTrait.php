<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\ManagerListener;

use Flower\View\Pane\PaneClass\PaneInterface;
use Flower\View\Pane\PaneEvent;
use Zend\EventManager\EventManagerInterface;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
trait PaneCacheTrait
{

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(PaneEvent::EVENT_GET_PANE, array($this, 'preGet'), 10000);
        $this->listeners[] = $events->attach(PaneEvent::EVENT_GET_PANE, array($this, 'postGet'), -10000);
        $this->listeners[] = $events->attach(PaneEvent::EVENT_REFRESH_PANE, array($this, 'onRefresh'));
    }

    public function preGet(PaneEvent $e)
    {
        $params = $e->getParams();
        if (isset($params['no_cache']) && $params['no_cache']) {
            return;
        }

        if (!$storage = $this->getStorage()) {
            return;
        }

        $paneId = $this->normalizeKey($e->getPaneId());

        if (! $storage->hasItem($paneId)) {
            return;
        }

        try {
            $serialized = $storage->getItem($paneId);
            $pane = $this->getSerializer()->unserialize($serialized);
        } catch (\Exception $ex) {
            $e->addErrorMessage($ex->getMessage() . ' at ' . $ex->getFile() . ' : ' . $ex->getLine());
            $storage->removeItem($paneId);
            return;
        }

        if (! $pane instanceof PaneInterface ){
            $e->addErrorMessage('failed to make pane from cached string ');
            $storage->removeItem($paneId);
            return;
        }

        $e->setResult($pane);

        $e->stopPropagation(true);

        return $pane;
    }

    public function postGet(PaneEvent $e)
    {
        if (!$e->hasResult()) {
            return;
        }

        $params = $e->getParams();
        if (isset($params['no_cache']) && $params['no_cache']) {
            return;
        }


        $pane = $e->getResult();

        if (!$pane instanceof PaneInterface) {
            return;
        }

        if ($pane->getOption('no_cache')) {
            return;
        }

        if ($e->hasError()) {
            return $pane;
        }

        if (!$storage = $this->getStorage()) {
            return $pane;
        }

        $paneId = $this->normalizeKey($e->getPaneId());

        try {
            $serialized = $this->getSerializer()->serialize($pane);
        } catch (\Exception $ex) {
            $e->addErrorMessage($ex->getMessage() . ' at ' . $ex->getFile() . ' : ' . $ex->getLine());
            //have callback?
            return $pane;
        }

        try {
            $storage->setItem($paneId, $serialized);
        } catch (\Exception $ex) {
            $e->addErrorMessage($ex->getMessage() . ' at ' . $ex->getFile() . ' : ' . $ex->getLine());
        }

        return $pane;
    }
}
