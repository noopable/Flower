<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Factory;

use Flower\View\Pane\PaneClass\PaneInterface;

/**
 * Description of ListPaneFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ListPaneFactory extends PaneFactory
{
    protected static $paneClass = 'Flower\View\Pane\PaneClass\ListPane';

    public static function parseBeginEnd(PaneInterface $pane, array $config)
    {
        if (isset($config['begin'])) {
            $pane->setBegin((string) $config['begin']);
        } elseif(!isset($pane->tag) || empty($pane->tag)) {
            $pane->setBegin('<!-- start pane -->');
        } else {
            $attributeString = self::parseAttributes($pane);
            if (strlen($attributeString)) {
                $pane->setBegin(sprintf('<%s%s>', $pane->tag, $attributeString));
            }
            else {
                $pane->setBegin(sprintf('<%s>', $pane->tag));
            }
         }

         if (isset($config['end'])) {
             $pane->setEnd((string) $config['end']);
         } elseif(! strlen($pane->tag)) {
             $pane->setEnd('<!-- end pane -->');
         } else {
             $pane->setEnd('</' . $pane->tag . '>');
         }
    }

    public static function parseWrapBeginEnd(PaneInterface $pane, array $config)
    {
        if (isset($config['wrapBegin'])) {
            $pane->setWrapBegin((string) $config['wrapBegin']);
        } elseif(!isset($pane->wrapTag) || empty($pane->wrapTag)) {
            $pane->setWrapBegin('<!-- start wrap pane -->');
        } else {
            $attributes = $pane->getOption('wrap_attributes');
            if (is_array($attributes)) {
                $attributeString = self::attributesToAttributeString($attributes, $pane->getOption('wrap_attr_options'));
                $pane->setWrapBegin(sprintf('<%s%s>', $pane->wrapTag, $attributeString));
            } else {
                $pane->setWrapBegin(sprintf('<%s>', $pane->wrapTag));
            }
        }

         if (isset ($config['wrapEnd'])) {
             $pane->setWrapEnd((string) $config['wrapEnd']);
         } elseif (! strlen($pane->wrapTag)) {
             $pane->setWrapEnd('<!-- end wrap pane -->');
         } else {
             $pane->setWrapEnd('</' . $pane->wrapTag . '>');
         }
    }

    public static function parseContainerBeginEnd(PaneInterface $pane, array $config)
    {
        if (isset($config['containerBegin'])) {
            $pane->setContainerBegin((string) $config['containerBegin']);
        } elseif(!isset($pane->containerTag) || empty($pane->containerTag)) {
            $pane->setContainerBegin('<!-- start container pane -->');
        } else {
            $attributes = $pane->getOption('container_attributes');
            if (is_array($attributes)) {
                $attributeString = self::attributesToAttributeString($attributes, $pane->getOption('container_attr_options'));
                $pane->setContainerBegin(sprintf('<%s%s>', $pane->containerTag, $attributeString));
            } else {
                $pane->setContainerBegin(sprintf('<%s>', $pane->containerTag));
            }
        }
         if (isset($config['containerEnd'])) {
             $pane->setContainerEnd((string) $config['containerEnd']);
         } elseif(!isset($pane->containerTag) || empty($pane->containerTag)) {
            $pane->setContainerEnd('<!-- end container pane -->');
         } else {
             $pane->setContainerEnd('</' . $pane->containerTag . '>');
         }
    }

    public static function treatment(PaneInterface $pane)
    {
        if (isset($pane->var) && ($pane->var !== array($pane, 'render'))) {
            $pane->_var = $pane->var;
            $pane->var = array($pane, 'render');
        }
    }
}
