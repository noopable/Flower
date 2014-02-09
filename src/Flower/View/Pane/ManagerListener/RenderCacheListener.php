<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\ManagerListener;

use Flower\View\Pane\PaneEvent;
use Zend\EventManager\EventManagerInterface;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class RenderCacheListener extends AbstractCacheListener
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
        $this->listeners[] = $events->attach(PaneEvent::EVENT_RENDER, array($this, 'preRender'), 10000);
        $this->listeners[] = $events->attach(PaneEvent::EVENT_RENDER, array($this, 'postRender'), -10000);
    }

    public function preRender(PaneEvent $e)
    {
        if (!$storage = $this->getStorage()) {
            return;
        }

        $paneId = $e->getPaneId();

        if (! $storage->hasItem($paneId)) {
            return;
        }

        try {
            $rendered = $storage->getItem($paneId);
        } catch (\Exception $ex) {
            $e->addErrorMessage($ex->getMessage() . ' at ' . $ex->getFile() . ' : ' . $ex->getLine());
            $storage->removeItem($paneId);
            return;
        }

        $e->setResult($rendered);

        $e->stopPropagation(true);

        return $rendered;
    }

    public function postRender(PaneEvent $e)
    {
        if (!$e->hasResult()) {
            return;
        }

        $rendered = $e->getResult();
        $paneId = $e->getPaneId();

        if ($e->hasError()) {
            return $rendered;
        }

        if (!$storage = $this->getStorage()) {
            return $rendered;
        }

        try {
            $storage->setItem($paneId, $rendered);
        } catch (\Exception $ex) {
            $e->addErrorMessage($ex->getMessage() . ' at ' . $ex->getFile() . ' : ' . $ex->getLine());
        }

        return $rendered;
    }

}
