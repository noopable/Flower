<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

return array(
    'pane_class' => 'Flower\View\Pane\PaneClass\Collection',
    'tag' => 'div',
    'order' => 5,
    'size' => 10,
    'var' => 'header',
    'classes' => 'container row',
    'prototype' => array(
        'pane_class' => 'Flower\View\Pane\PaneClass\EntityScriptPane',
        'var' => 'entity',
    ),
    'attributes' => array(
        'foo' => 'bar',
        'baz' => 'qux',
    ),
);




