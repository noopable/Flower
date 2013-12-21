<?php
namespace Flower\View\Pane;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use RecursiveIterator;
/**
 *
 * @author tomoaki
 */
interface PaneInterface extends RecursiveIterator {
    public function getOrder();
    
    /**
     * 
     * @param type $value
     * @param type $priority
     * @return type
     */
    public function insert($value, $priority = null);
}
