<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Factory;

use Flower\View\Pane\PaneClass\CollectionAwareInterface;
use Flower\View\Pane\PaneClass\EntityAwareInterface;
use Flower\View\Pane\PaneClass\PaneInterface;

/**
 * Description of EntityScriptPaneFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class CollectionFactory extends ListPaneFactory
{
    protected static $paneClass = 'Flower\View\Pane\PaneClass\Collection';

    public static function parseConfig(PaneInterface $pane, array $config)
    {
        foreach ($config as $k => $v) {
            switch ($k) {
                case "prototype":
                    if (($pane instanceof EntityPrototypeAwareInterface) && ($v instanceof EntityAwareInterface)) {
                        $pane->setPrototype($v);
                    }
                    unset($config[$k]);
                    break;
                case "collection":
                    if ($pane instanceof CollectionAwareInterface) {
                        $pane->setCollection($v);
                    }
                    unset($config[$k]);
                    break;
                default:
                    break;
            }
        }

        parent::parseConfig($pane, $config);
    }
}
