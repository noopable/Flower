<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Service;

/**
 * Description of ListHelperFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class AnchorHelperFactory extends HelperFactory
{
    protected $configKey = 'flower_anchor_pane';

    protected $helperClass = 'Flower\View\Pane\Service\AnchorHelper';
}
