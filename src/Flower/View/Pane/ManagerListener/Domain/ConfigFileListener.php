<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\ManagerListener\Domain;

use Flower\File\Gateway\GatewayInterface;
use Flower\View\Pane\PaneEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Stdlib\ArrayUtils;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ConfigFileListener extends AbstractListenerAggregate
{
    protected $fileService;

    public function setFileService(GatewayInterface $fileService)
    {
        $this->fileService = $fileService;
    }

    public function getFileService()
    {
        return $this->fileService;
    }

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
        $this->listeners[] = $events->attach(PaneEvent::EVENT_LOAD_CONFIG, array($this, 'onload'), 10);
    }

    public function onLoad(PaneEvent $e)
    {
        if (!isset($this->fileService)) {
            return;
        }

        $paneId = $e->getPaneId();

        $config = $this->getFileService()->read($paneId);
        if (is_array($config) && !empty($config)) {
            if ($e->hasResult()) {
                $pre = $e->getResult();
                if (is_array($pre)) {
                    $config = ArrayUtils::merge($pre, $config);
                }
            }
            $e->setResult($config);
        }
        return $config;
    }
}
