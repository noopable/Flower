<?php
namespace Flower\View\Navigation;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use RecursiveFilterIterator;
/**
 * Description of FilterContainer
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class FilterContainer extends RecursiveFilterIterator
{

    protected $filter;

    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    public function accept()
    {
        if (!is_callable($this->filter)) {
            return true;
        }
        return $this->filter($this->getInnerIterator());
    }
}
