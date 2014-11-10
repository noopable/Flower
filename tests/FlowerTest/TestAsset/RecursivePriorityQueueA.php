<?php
/*
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace FlowerTest\TestAsset;

use Flower\RecursivePriorityQueueTrait;
use RecursiveIterator;

/**
 * Description of RecursivePriorityQueueA
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class RecursivePriorityQueueA implements RecursiveIterator {
    use RecursivePriorityQueueTrait;
    
}
