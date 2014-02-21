<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Entity;

use ArrayIterator;
use Flower\View\Pane\Entity\ApplicatableCallbackEntity;
use IteratorAggregate;
/**
 * Description of ApplicatableCallbackCollection
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ApplicatableCallbackCollection implements IteratorAggregate
{

    protected $iterator;

    public function __construct($callback, array $array)
    {
        $this->iterator = new ArrayIterator(array());
        foreach($array as $params) {
            $this->iterator->append(new ApplicatableCallbackEntity($callback, $params));
        }
    }

    public function getIterator()
    {
        return $this->iterator;
    }
}
