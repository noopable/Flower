<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane;

/**
 * Description of RendererConfig
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class RendererConfig
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function configure(PaneRenderer $paneRenderer)
    {
        $config = $this->config;
        if (isset($config['comment_enable'])) {
            $paneRenderer->commentEnable = (bool) $config['comment_enable'];
        }

        if (isset($config['indent'])) {
            $paneRenderer->indent = $config['indent'];
        }

        if (isset($config['linefeed'])) {
            $paneRenderer->linefeed = $config['linefeed'];
        }

        if (isset($config['max_depth'])) {
            $paneRenderer->setMaxDepth((int) $config['max_depth']);
        }
    }
}
