<?php
namespace Flower\View\Pane;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

use Zend\Stdlib\ArrayUtils;
use Zend\Escaper\Escaper;
use Flower\Exception\IllegalClassException;
/**
 * Description of Builder
 *
 * @author tomoaki
 */
class Builder {
    protected $paneClass = 'Flower\View\Pane\Pane';

    protected $sizeToClassFunction;
    
    protected $escaper;
    
    public function __construct($options = array())
    {
        if (isset($options['pane_class'])) {
            $this->paneClass = $options['pane_class'];
        }
        
        if (isset($options['size_to_class_function']) && is_callable($options['size_to_class_function'])) {
            $this->sizeToClassFunction = $options['size_to_class_function'];
        }
    }
    
    public function setPaneClass($pane)
    {
        if ($pane instanceof PaneInterface) {
            $this->paneClass = get_class($pane);
        } elseif (is_string($pane)) {
            if (!is_subclass_of($pane, 'Flower\View\Pane\PaneInterface', true)) {
                throw new IllegalClassException('Specified class name is ' .  $pane . ' but not implements PaneInterface');
            }
            $this->paneClass = $pane;
        }
        
        return $this;
    }
    

    /**
     * これから作成しようとするPaneの設定を$configに配列で渡す。
     * 設定から直接Paneをビルドするときは、$current = nullとして渡す。
     * 既存のPaneがあり、子を追加したいときには、$current = $parentPaneとして渡す
     * 
     * 出来上がったPane　$currentを返します。
     * 
     * @param array $config pane structure configuration
     * @param PaneInterface|null $current 
     * @return PaneInterface
     */
    public function build(array $config, PaneInterface $current = null)
    {
        if (null === $current) {
            $current = $this->getNewPane();
        }
        
        foreach ($config as $k => $v) {
            if ($v instanceof Pane) {
                //direct pane insert ,ignore $type
                $current->insert($v, $v->getOrder());
                continue;
            }
            switch ($k) {
                case "order":
                case "size":
                    $current->$k = (int) $v;
                    break;
                case "var":
                    if ($v instanceof \Closure || is_string($v)) {
                        $current->$k = $v;
                    } else {
                        $current->$k = false;
                    }
                    break;
                case "id":
                case "tag":
                    $current->$k = (string) $v;
                    break;
                case "classes":
                case "attributes":
                    $current->$k = (array) $v;
                    break;
                case "inner":
                    if ($v instanceof Pane) {
                        $current->insert($v, $v->getOrder());
                    }
                    elseif (ArrayUtils::isList($v)) {
                        foreach ($v as $c) {
                            $child = $this->build($c);
                            $current->insert($child, $child->getOrder());
                        }
                    }
                    elseif(is_array($v)) {
                        $child = $this->build($v);
                        $current->insert($child, $child->getOrder());
                    }
                    break;
                case "begin":
                case "end":
                default:
                    break;
            }
        }
        if (isset($config['begin'])) {
            $current->begin = (string) $config['begin'];
        }
        elseif(! strlen($current->tag)) {
            $current->begin = '<!-- start pane -->' . PHP_EOL;
        }
        else {
            $attributes = $current->attributes ?: array();

            if (isset($current->size)) {
                $this->addHtmlClass($this->sizeToClass($current->size), $attributes);
            }

            if (isset($current->classes)) {
                $this->addHtmlClass(implode(' ', $current->classes), $attributes);
            }

            if (isset($current->id)) {
                $attributes['id'] = $current->id;
            }

            $attributeString = '';

            foreach($attributes as $name => $attribute) {
                $attributeString .= ' ' . $name . '=\'' . trim($attribute) . '\'';
            }
            if (strlen($attributeString)) {
                $current->begin = sprintf('<%s%s>', $current->tag, $attributeString) . PHP_EOL;
            }
            else {
                $current->begin = sprintf('<%s>', $current->tag) . PHP_EOL;
            }
         }

         if (isset($config['end'])) {
             $current->end = (string) $config['end'] . PHP_EOL;
         }
         elseif(! strlen($current->tag)) {
             $current->end = '<!-- end pane -->' . PHP_EOL;
         }
         else {
             $current->end = '</' . $current->tag . '>' . PHP_EOL;
         }
         
         return $current;
    }
    
    public function getNewPane()
    {
        return new $this->paneClass;
    }
    
    /**
     * 
     * @param type $class
     * @param array $attributes
     */
    protected function addHtmlClass($class, array &$attributes)
    {
        $aClass = explode(' ', $class);
        array_map(array($this->getEscaper(), 'escapeHtmlAttr'), $aClass);
        $class = implode(' ', $aClass);
        
        if (!isset($attributes['class']) || !strlen($attributes['class'])) {
            $attributes['class'] = $class;
        } else {
            $attributes['class'] .= ' ' . $class;
        }
    }
    
    /**
     * for util
     * pane size to class 
     * 
     * @param mixed $size
     * @return string $class
     */
    protected function sizeToClass($size = 0)
    {

        if (is_callable($this->sizeToClassFunction)) {
            $class = call_user_func($this->sizeToClassFunction, $size);
        } else {
            //default for twitter bootsrap 2
            // convert to small decimal string
            $class = 'span' . (string) (intval($size) % 36);
        }
        return $class;
    }
    
    protected function getEscaper()
    {
        if (!isset($this->escaper)) {
            $this->escaper = new Escaper;
        }
        return $this->escaper;
    }
}
