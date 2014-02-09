<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Service;

use Zend\ServiceManager\FactoryInterface;

/**
 * Description of ConfigFileListenerFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class RenderCacheListenerFactory extends AbstractCacheListenerFactory
{
    protected $configKey = 'render_cache_listener';

    protected $defaultListenerClass = 'Flower\View\Pane\Service\RenderCacheListener';

}
