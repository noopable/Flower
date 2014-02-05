<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Factory;

use Zend\Escaper\Escaper;
use Flower\View\Pane\PaneClass\PaneInterface;

/**
 * Description of PaneFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class PaneFactory implements PaneFactoryInterface
{

    protected static $sizeToClassFunction;

    protected static $escaper;

    protected static $paneClass = 'Flower\View\Pane\PaneClass\Pane';

    public static function factory(array $config)
    {
        /* @var $pane \Flower\View\Pane\PaneClass\PaneInterface  */
        if (isset($config['pane_class'])) {
            $pane = new $config['pane_class'];
        } else {
            $pane = new static::$paneClass;
        }

        static::parseConfig($pane, $config);

        static::parseBeginEnd($pane, $config);

        static::parseWrapBeginEnd($pane, $config);

        static::parseContainerBeginEnd($pane, $config);

        static::treatment($pane);

        return $pane;
    }

    public static function parseConfig(PaneInterface $pane, array $config)
    {
        //parse config about pane
        foreach ($config as $k => $v) {
            if ($v instanceof PaneInterface) {
                //direct pane insert ,ignore $type
                $pane->insert($v, $v->getOrder());
                continue;
            }
            switch ($k) {
                case "pane_id":
                    $pane->setPaneId($v);
                    break;
                case "size_to_class_function":
                    if (is_callable($v) && method_exists($pane, 'setSizeToClassFunction')) {
                        $pane->setSizeToClassFunction($v);
                    }
                    break;
                case "classes":
                    if (is_array($v) || is_string($v)) {
                        $pane->$k = $v;
                    }
                    break;
                case "label":
                case "name":
                    $pane->$k = (string) $v;
                    break;
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
                    $pane->$k = preg_replace(array('/^[^a-z][^a-z]*/i', '/[^-a-z0-9_:.]*/i'), '', (string) $v);
                    break;
                case "tag":
                case "wrapTag":
                case "containerTag":
                    if (is_string($v)) {
                        $pane->$k = preg_replace(array('/^[^a-z_:][^a-z_:]*/i', '/[^-a-z0-9_:]*/i'), '', $v);
                    } else {
                        $pane->$k = $v;
                    }
                    break;
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
            $pane->setWrapBegin('<!-- start pane -->');
        } else {
            $attributeString = self::parseAttributes($pane);
            if (strlen($attributeString)) {
                $pane->setWrapBegin(sprintf('<%s%s>', $pane->wrapTag, $attributeString));
            } else {
                $pane->setWrapBegin(sprintf('<%s>', $pane->wrapTag));
            }
         }

         if (isset ($config['wrapEnd'])) {
             $pane->setWrapEnd((string) $config['wrapEnd']);
         } elseif (! strlen($pane->wrapTag)) {
             $pane->setWrapEnd('<!-- end pane -->');
         } else {
             $pane->setWrapEnd('</' . $pane->wrapTag . '>');
         }
    }

    public static function parseContainerBeginEnd(PaneInterface $pane, array $config)
    {
    }

    public static function parseAttributes(PaneInterface $pane)
    {
        $attributes = $pane->attributes ?: array();

        if (isset($pane->id)) {
            $attributes['id'] = $pane->id;
        }

        if (isset($pane->name)) {
            $attributes['name'] = $pane->name;
        }

        if (isset($pane->size) && method_exists($pane, 'sizeToClass')) {
            self::addHtmlClass($pane->sizeToClass($pane->size), $attributes);
        }

        if (isset($pane->classes)) {
            self::addHtmlClass($pane->classes, $attributes);
        }

        return self::attributesToAttributeString($attributes);
    }

    public static function attributesToAttributeString(array $attributes)
    {
        $attributeString = '';
        $escaper = self::getEscaper();
        foreach($attributes as $name => $attribute) {
            $name = preg_replace(array('/^[^a-z_:][^a-z_:]*/i', '/[^-a-z0-9_:\.]*/i'), '', $name);
            if (is_string($attribute) || is_numeric($attribute)) {
                switch (strtolower($name)) {
                    case 'href':
                    case 'src':
                        $delimiter = '/';
                        $escapeMethod = 'escapeUrl';
                        break;
                    default:
                        $delimiter = ' ';
                        $escapeMethod = 'escapeHtmlAttr';
                        break;
                }
                $attributeArray = array_map(array($escaper, $escapeMethod), explode($delimiter, $attribute));
                $attributeString .= ' ' . $name . '="' . implode($delimiter, $attributeArray) . '"';
            } elseif (!$attribute) {
                $attributeString .= ' ' . $name;
            }
        }

        return $attributeString;
    }

    public static function treatment(PaneInterface $pane)
    {
    }

    /**
     *
     * @param type $class
     * @param array $attributes
     */
    public static function addHtmlClass($class, array &$attributes)
    {
        if (is_array($class)) {
            $class = implode(' ', $class);
        }

        if (!isset($attributes['class']) || !strlen($attributes['class'])) {
            $attributes['class'] = $class;
        } else {
            $attributes['class'] .= ' ' . $class;
        }
    }

    public static function getEscaper()
    {
        if (!isset(self::$escaper)) {
            self::$escaper = new Escaper;
        }
        return self::$escaper;
    }
}
