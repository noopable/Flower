<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Entity;

use Flower\View\Pane\PaneClass\PaneInterface;
/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface ApplicatePaneInterface
{
    public function apply(PaneInterface $pane);
}
