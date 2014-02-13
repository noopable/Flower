<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace FlowerTest\View\Pane\ManagerListener\TestAsset;

use Flower\View\Pane\ManagerListener\AbstractCacheListener;
/**
 * Description of ConcreteLazyLoadCacheListener
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ConcreteCacheListener extends AbstractCacheListener
{
    public function attach(\Zend\EventManager\EventManagerInterface $events)
    {

    }

}
