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
    const EVENT_BUILD_PANE = 'build_pane';
    const EVENT_LOAD_CONFIG = 'load_config';

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

}

