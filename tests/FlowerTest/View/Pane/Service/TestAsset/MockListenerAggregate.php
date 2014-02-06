<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace FlowerTest\View\Pane\Service\TestAsset;

use Flower\View\Pane\PaneEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;


/**
 * Description of MockListenerAggregate
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class MockListenerAggregate extends AbstractListenerAggregate
{

    public function attach(EventManagerInterface $events)
    {
        $event = PaneEvent::EVENT_BUILD_PANE;
        $callback = array($this, 'onEvent');
        $priority = null;
        $this->listeners[] = $events->attach($event, $callback, $priority);
    }

    public function onEvent(Event $e)
    {
    }
}
