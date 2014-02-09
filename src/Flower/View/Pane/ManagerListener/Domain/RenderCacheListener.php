<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\ManagerListener\Domain;

use Flower\View\Pane\ManagerListener\AbstractLazyLoadCacheListener;
use Flower\View\Pane\ManagerListener\RenderCacheTrait;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class RenderCacheListener extends AbstractLazyLoadCacheListener
{
    use RenderCacheTrait;
}