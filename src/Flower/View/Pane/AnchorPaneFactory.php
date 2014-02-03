<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane;

/**
 * Description of AnchorPaneFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class AnchorPaneFactory extends ListPaneFactory
{
    protected static $paneClass = 'Flower\View\Pane\Anchor';

    public static function parseConfig(PaneInterface $pane, array $config)
    {
        parent::parseConfig($pane, $config);
        //parse config
        foreach ($config as $k => $v) {
            switch ($k) {
                case "route":
                case "controller":
                case "action":
                    $pane->$k = (string) $v;
                    break;
                case "params":
                    $pane->$k = (array) $v;
                    break;
                default:
                    break;
            }
        }
    }
}
