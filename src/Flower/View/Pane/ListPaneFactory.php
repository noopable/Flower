<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane;

/**
 * Description of ListPaneFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ListPaneFactory extends PaneFactory
{
    protected static $paneClass = 'Flower\View\Pane\ListPane';

    public static function factory(array $config, Builder $builder)
    {
        /* @var $pane \Flower\View\Pane\PaneInterface  */
        if (isset($config['pane_class'])) {
            $pane = new $config['pane_class'];
        } else {
            $pane = new static::$paneClass;
        }

        self::parseConfig($pane, $config);

        if (isset($pane->var)) {
            $pane->_var = $pane->var;
        } else {
            $pane->_var = 'content';
        }

        $pane->var = array($pane, 'render');

        if (isset($config['begin'])) {
            $pane->setBegin((string) $config['begin']);
        } else {
            $attributeString = self::parseAttributes($pane, $builder);
            if (strlen($attributeString)) {
                $pane->setBegin(sprintf('<%s%s>', $pane->tag, $attributeString));
            }
            else {
                $pane->setBegin(sprintf('<%s>', $pane->tag));
            }
         }

        if (isset($config['wrapBegin'])) {
            $pane->setWrapBegin((string) $config['wrapBegin']);
        } else {
            $attributes = $pane->getOption('wrap_attributes');
            if (is_array($attributes)) {
                $attributeString = self::attributesToAttributeString($pane, $builder);
                $pane->setWrapBegin(sprintf('<%s%s>', $pane->wrapTag, $attributeString));
            } else {
                $pane->setWrapBegin(sprintf('<%s>', $pane->wrapTag));
            }
        }

        if (isset($config['containerBegin'])) {
            $pane->containerBegin = (string) $config['containerBegin'];
        } elseif(isset($pane->containerTag)) {
            $attributes = $pane->getOption('container_attributes');
            if (is_array($attributes)) {
                $attributeString = self::attributesToAttributeString($pane, $builder);
                $pane->containerBegin = sprintf('<%s%s>', $pane->containerTag, $attributeString);
            } else {
                $pane->containerBegin = sprintf('<%s>', $pane->containerTag);
            }
        }

         if (isset($config['end'])) {
             $pane->setEnd((string) $config['end']);
         } elseif(! strlen($pane->tag)) {
             $pane->setEnd('<!-- end pane -->');
         } else {
             $pane->setEnd('</' . $pane->tag . '>');
         }

         if (isset($config['wrapEnd'])) {
             $pane->setWrapEnd((string) $config['wrapEnd']);
         } elseif (! strlen($pane->wrapTag)) {
             $pane->setWrapEnd('<!-- end pane -->');
         } else {
             $pane->setWrapEnd('</' . $pane->wrapTag . '>');
         }

         if (isset($config['containerEnd'])) {
             $pane->containerEnd = (string) $config['containerEnd'];
         } elseif (! isset($pane->containerTag)) {
             $pane->containerEnd = '<!-- end container -->';
         } else {
             $pane->containerEnd = '</' . $pane->containerTag . '>';
         }
         return $pane;
    }
}
