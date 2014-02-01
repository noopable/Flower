<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane;

/**
 * Description of PaneFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class PaneFactory implements PaneFactoryInterface
{

    protected static $escaper;

    protected static $paneClass = 'Flower\View\Pane\Pane';

    public static function factory(array $config, Builder $builder)
    {
        /* @var $pane \Flower\View\Pane\PaneInterface  */
        if (isset($config['pane_class'])) {
            $pane = new $config['pane_class'];
        } else {
            $pane = new self::$paneClass;
        }

        self::parseConfig($pane, $config);

        if (isset($config['begin'])) {
            $pane->setBegin((string) $config['begin']);
        } elseif(!isset($pane->tag) || empty($pane->tag)) {
            $pane->setBegin('<!-- start pane -->' . PHP_EOL);
        } else {
            $attributeString = self::parseAttributes($pane, $builder);
            if (strlen($attributeString)) {
                $pane->setBegin(sprintf('<%s%s>', $pane->tag, $attributeString) . PHP_EOL);
            }
            else {
                $pane->setBegin(sprintf('<%s>', $pane->tag) . PHP_EOL);
            }
         }

        if (isset($config['wrapBegin'])) {
            $pane->setWrapBegin((string) $config['wrapBegin']);
        } elseif(!isset($pane->wrapTag) || empty($pane->wrapTag)) {
            $pane->setWrapBegin('<!-- start pane -->' . PHP_EOL);
        } else {
            if (!isset($attributeString)) {
                $attributeString = self::parseAttributes($pane, $builder);
            }
            if (strlen($attributeString)) {
                $pane->setWrapBegin(sprintf('<%s%s>', $pane->wrapTag, $attributeString) . PHP_EOL);
            }
            else {
                $pane->setWrapBegin(sprintf('<%s>', $pane->wrapTag) . PHP_EOL);
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
         } elseif(! strlen($pane->wrapTag)) {
             $pane->setWrapEnd('<!-- end pane -->');
         } else {
             $pane->setWrapEnd('</' . $pane->wrapTag . '>');
         }

         return $pane;
    }

    public static function parseConfig(PaneInterface $pane, array $config)
    {
        //parse config
        foreach ($config as $k => $v) {
            if ($v instanceof PaneInterface) {
                //direct pane insert ,ignore $type
                $pane->insert($v, $v->getOrder());
                continue;
            }
            switch ($k) {
                case "order":
                case "size":
                    $pane->$k = (int) $v;
                    break;
                case "var":
                    if ($v instanceof \Closure || is_string($v) || is_callable($v)) {
                        $pane->$k = $v;
                    } else {
                        $pane->$k = false;
                    }
                    break;
                case "id":
                case "tag":
                case "wrapTag":
                    if (is_string($v)) {
                        $pane->$k = preg_replace(array('/^[^a-z_:][^a-z_:]*/i', '/[^-a-z0-9_:]*/i'), '', $v);
                    } else {
                        $pane->$k = $v;
                    }
                    break;
                case "classes":
                case "attributes":
                    if (is_string($v)) {
                        $v = explode(' ', $v);
                    }
                    $pane->$k = $v;
                    break;
                case "options":
                    //配列型の他各種データを受け入れてよい。
                    $pane->setOptions($v);
                    break;
                case "begin":
                case "end":
                case "wrapBegin":
                case "wrapEnd":
                default:
                    break;
            }
        }
        if (!isset($pane->wrapTag)) {
            $pane->wrapTag = $pane->tag;
        }
    }

    public static function parseAttributes(PaneInterface $pane, $builder)
    {
        $attributes = $pane->attributes ?: array();

        if (isset($pane->id)) {
            $attributes['id'] = $pane->id;
        }

        if (isset($pane->size)) {
            $builder->addHtmlClass($builder->sizeToClass($pane->size), $attributes);
        }

        if (isset($pane->classes)) {
            $builder->addHtmlClass($pane->classes, $attributes);
        }

        $attributeString = '';

        foreach($attributes as $name => $attribute) {
            if (is_string($attribute) || is_numeric($attribute)) {
                $attributeArray = array_map(array($builder->getEscaper(), 'escapeHtmlAttr'), explode(' ', $attribute));
                $attributeString .= ' ' . preg_replace(array('/^[^a-z_:][^a-z_:]*/i', '/[^-a-z0-9_:]*/i'), '', (string)$name)
                                        . '="' . implode(' ', $attributeArray) . '"';
            } elseif (!$attribute) {
                $attributeString .= ' ' . $name;
            }
        }

        return $attributeString;
    }
}
