<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\PaneClass;

/**
 * Description of PanePublicTrait
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
trait PanePublicTrait
{
    public $id;

    public $name;

    /**
     *
     * @var array
     */
    public $classes;

    /**
     *
     * @var array
     */
    public $attributes;

    public $order = 1;

    public $size;

    public $label;

    public $var = 'content';

    // tag names
    public $tag;

    public $wrapTag;

    public $containerTag;
}
