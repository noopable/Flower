<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Factory;

use Flower\View\Pane\PaneClass\PaneInterface;

/**
 * Description of AnchorPaneFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class AnchorPaneFactory extends ListPaneFactory
{
    protected static $paneClass = 'Flower\View\Pane\PaneClass\Anchor';

    public static function parseBeginEnd(PaneInterface $pane, array $config)
    {
        if (isset($config['begin'])) {
            $pane->setBegin((string) $config['begin']);
        } elseif(!isset($pane->tag) || empty($pane->tag)) {
            $pane->setBegin('<!-- start pane -->');
        } else {
            if ($href = $pane->getHref()) {
                //hrefの割り当てを試す
                $tag = $pane->tag;
                $pane->attributes['href'] = $href;
            } else {
                $tag = $pane->getSubstituteTag();
            }

            $attributeString = self::parseAttributes($pane);

            if (isset($attributeString)) {
                $pane->setBegin(sprintf('<%s%s>', $tag, $attributeString));
            } else {
                $pane->setBegin('<' . $tag . '>');
            }
         }

         if (isset($config['end'])) {
             $pane->setEnd((string) $config['end']);
         } elseif(! strlen($pane->tag)) {
             $pane->setEnd('<!-- end pane -->');
         } else {
             $pane->setEnd('</' . $tag . '>');
         }
    }

    public static function parseConfig(PaneInterface $pane, array $config)
    {
        parent::parseConfig($pane, $config);
        //parse config
        foreach ($config as $k => $v) {
            switch ($k) {
                case "href":
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
