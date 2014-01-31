<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane;

use RecursiveIterator;

/**
 *
 * @author tomoaki
 */
interface PaneInterface extends RecursiveIterator
{
    public function getOrder();

    /**
     *
     * @param type $value
     * @param type $priority
     * @return type
     */
    public function insert($value, $priority = null);

    public function begin();

    public function end();

    public function setBegin($begin);

    public function setEnd($end);
}
