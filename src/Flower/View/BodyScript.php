<?php

/*
 * 
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View;

use Zend\View\Helper\HeadScript;
/**
 * Helper for setting and retrieving script elements for HTML head section
 *
 * Allows the following method calls:
 * @method HeadScript appendFile($src, $type = 'text/javascript', $attrs = array())
 * @method HeadScript offsetSetFile($index, $src, $type = 'text/javascript', $attrs = array())
 * @method HeadScript prependFile($src, $type = 'text/javascript', $attrs = array())
 * @method HeadScript setFile($src, $type = 'text/javascript', $attrs = array())
 * @method HeadScript appendScript($script, $type = 'text/javascript', $attrs = array())
 * @method HeadScript offsetSetScript($index, $src, $type = 'text/javascript', $attrs = array())
 * @method HeadScript prependScript($script, $type = 'text/javascript', $attrs = array())
 * @method HeadScript setScript($script, $type = 'text/javascript', $attrs = array())
 */
class BodyScript extends HeadScript {
    /**
     * Registry key for placeholder
     *
     * @var string
     */
    protected $regKey = 'Flower_View_BodyScript';
}
