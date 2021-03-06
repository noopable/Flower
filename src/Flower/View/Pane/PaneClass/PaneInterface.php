<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\PaneClass;

use Flower\View\Pane\PaneRenderer;
use RecursiveIterator;

/**
 *
 * @author tomoaki
 */
interface PaneInterface extends RecursiveIterator
{
    /**
     * @return \Flower\View\Pane\Factory\PaneFactoryInterface
     */
    public static function getFactoryClass();

    public function getPaneId();

    public function setPaneId($paneId);

    public function getOrder();

    /**
     *
     * @param type $value
     * @param type $priority
     * @return type
     */
    public function insert($value, $priority = null);

    public function begin($depth = null);

    public function end($depth = null);

    public function wrapBegin($depth = null);

    public function wrapEnd($depth = null);

    public function containerBegin($depth = null);

    public function containerEnd($depth = null);

    public function setBegin($begin);

    public function setEnd($end);

    public function setWrapBegin($wrapBegin);

    public function setWrapEnd($wrapEnd);

    public function setContainerBegin($containerBegin);

    public function setContainerEnd($containerEnd);

    public function setOptions(array $options);

    public function getOptions();

    public function setOption($name, $option);

    public function getOption($name);

    public function setPaneRenderer(PaneRenderer $paneRenderer);

    public function getPaneRenderer();

    public function setRegistry($registry);

    public function getRegistry();

    public function hasContent();
}
