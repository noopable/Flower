<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane;

use Zend\EventManager\Event;

/**
 * Description of PaneEvent
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class PaneEvent extends Event
{
    const EVENT_GET_PANE = 'get_pane';
    const EVENT_LOAD_CONFIG = 'load_config';
    const EVENT_BUILD_PANE = 'build_pane';
    const EVENT_RENDER = 'render_pane';
    const EVENT_REFRESH_CONFIG = 'refresh_config';
    const EVENT_REFRESH_PANE = 'refresh_pane';
    const EVENT_REFRESH_RENDER = 'refresh_render';

    /**
     * @var \Flower\Pane\PaneManager
     */
    protected $manager;


    protected $paneId;

    /**
     * @var mixed
     */
    protected $result;

    /**
     *
     * @var string
     */
    protected $errorMessages = array();

    /**
     * @param PaneManager $manager
     */
    public function setManager(PaneManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return PaneManager
     */
    public function getManager()
    {
        return $this->manager;
    }

    public function setPaneId($paneId)
    {
        $this->paneId = $paneId;
    }

    public function getPaneId()
    {
        return $this->paneId;
    }

    public function hasError()
    {
        return !empty($this->errorMessages);
    }

    public function addErrorMessage($message)
    {
        $this->errorMessages[] = $message;
    }

    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    public function setResult($result)
    {
        $this->result = $result;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function hasResult()
    {
        return isset($this->result);
    }

}

