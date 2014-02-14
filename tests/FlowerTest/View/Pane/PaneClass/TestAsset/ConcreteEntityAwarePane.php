<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace FlowerTest\View\Pane\PaneClass\TestAsset;

use Flower\View\Pane\PaneClass\EntityAwareInterface;
use Flower\View\Pane\PaneClass\EntityAwareTrait;
use Flower\View\Pane\PaneClass\Pane;
/**
 * Description of ConcreteEntityAwarePane
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ConcreteEntityAwarePane extends Pane implements EntityAwareInterface
{
    use EntityAwareTrait;
}
