<?php
namespace Flower\View;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Flower\RecursivePriorityQueue;
use Zend\Stdlib\ArrayUtils;

/**
 * これはpaneHelperを使うことで、ビュースクリプトからレイアウト要件を設定ファイル化できることにより、
 * レイアウトファイルの使いまわしを行動に引き上げる。
 * また、ブロックのネストを最小限にできる。
 *
 * SplPriorityQueueはrewindできないので、イテレーターの使いまわしができない。
 * 繰り返し処理で使うことを考えると、PriorityQueueではない解決方法を考える必要がありそう。
 *
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 *
 */
class Pane extends RecursivePriorityQueue// implements RecursiveIterator
{
    public $id;

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

    public $var = 'content';

    public $begin;

    public $end;

    public $tag = 'div';

    public function __construct(Pane $parent = null)
    {
        if (null !== $parent) {
            $this->parent = $parent;
        }
        parent::__construct(RecursivePriorityQueue::HAS_CHILDREN_STRICT_CONTAINS);
    }

    public function build(array $array)
    {
        foreach ($array as $k => $v) {
            if ($v instanceof Pane) {
                //direct pane insert ,ignore $type
                $this->insert($v, $v->getOrder());
                continue;
            }
            switch ($k) {
                case "order":
                case "size":
                    $this->$k = (int) $v;
                    break;
                case "var":
                    if ($v instanceof \Closure || is_string($v)) {
                        $this->$k = $v;
                    }
                    break;
                case "id":
                case "tag":
                    $this->$k = (string) $v;
                    break;
                case "classes":
                case "attributes":
                    $this->$k = (array) $v;
                    break;
                case "inner":
                    if ($v instanceof Pane) {
                        $this->insert($v, $v->getOrder());
                    }
                    elseif (ArrayUtils::isList($v)) {
                        foreach ($v as $c) {
                            if (is_array($c)) {
                                $child = new self;
                                $child->build($c);
                                $this->insert($child, $child->getOrder());
                            }
                            elseif ($c instanceof Pane) {
                                $this->insert($c, $c->getOrder());
                            }
                        }
                    }
                    elseif(is_array($v)) {
                        $child = new self;
                        $child->build($v);
                        $this->insert($child, $child->getOrder());
                    }
                    break;
                case "begin":
                case "end":
                default:
                    break;
            }
        }
        if (isset($array['begin'])) {
            $this->begin = (string) $array['begin'];
        }
        elseif(! strlen($this->tag)) {
            $this->begin = '<!-- start pane -->' . PHP_EOL;
        }
        else {
            $attributes = $this->attributes ?: array();

            if (isset($this->size)) {
                if (!isset($attriutes['class'])) {
                    $attributes['class'] = '';
                }
                //for twitter bootsrap auto caluculater
                $attributes['class'] .= ' span' . (string) $this->size;
            }

            if (isset($this->classes)) {
                if (!isset($attriutes['class'])) {
                    $attributes['class'] = '';
                }
                $attributes['class'] .= ' ' . implode(' ', $this->classes);
            }

            if (isset($this->id)) {
                $attributes['id'] = $this->id;
            }

            $attributeString = '';

            foreach($attributes as $name => $attribute) {
                $attributeString .= ' ' . $name . '=\'' . trim($attribute) . '\'';
            }
            if (strlen($attributeString)) {
                $this->begin = sprintf('<%s%s>', $this->tag, $attributeString) . PHP_EOL;
            }
            else {
                $this->begin = sprintf('<%s>', $this->tag) . PHP_EOL;
            }
         }

         if (isset($array['end'])) {
             $this->end = (string) $array['end'] . PHP_EOL;;
         }
         elseif(! strlen($this->tag)) {
             $this->end = '<!-- end pane -->' . PHP_EOL;
         }
         else {
             $this->end = '</' . $this->tag . '>' . PHP_EOL;
         }

    }

    public function getOrder()
    {
        return $this->order;
    }

    /**
     * 
     * @param type $value
     * @param type $priority
     * @return type
     */
    public function insert($value, $priority = null)
    {
        if (null === $priority) {
            if (is_object($value) && method_exists($value, 'getOrder')) {
                $priority = $value->getOrder();
            }
        }
        
        return parent::insert($value, $priority);
    }

}