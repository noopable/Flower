<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace FlowerTest\Domain\TestAsset;

use Flower\Domain\DomainAwareInterface;
use Flower\Domain\DomainAwareTrait;
/**
 * Description of ConcreteDomainAware
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ConcreteDomainAware implements DomainAwareInterface {
    use DomainAwareTrait;
}
