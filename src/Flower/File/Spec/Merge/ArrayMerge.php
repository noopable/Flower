<?php
namespace Flower\File\Spec\Merge;
/*
 * 
 * 
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

/**
 * treat data as array
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ArrayMerge implements MergeSpecInterface
{
    
    protected $breakOnFailure = false;
    /**
     * 
     * @param mixed $v1
     * @param mixed $v2
     * @return array
     */
    public function merge($v1, $v2)
    {
        $args = array($v1, $v2);
        $breakOnFailure = $this->breakOnFailure;
        $arrays = array_map(    
            function ($v) use ($breakOnFailure) {
                if (is_array($v)) {
                    return $v;
                }
                
                if ($breakOnFailure) {
                    return array();
                }
                
                if (is_scalar($v)) {
                    return array($v);
                }
                if (is_object($v)) {
                    if (method_exists($v, 'toArray')) {
                        return $v->toArray();
                    }
                    if ($v instanceof \Traversable) {
                        return ArrayUtils::iteratorToArray($v);
                    }
                }
                return array();
            },
            $args
        );
            
        return call_user_func_array('Zend\Stdlib\ArrayUtils::merge', $arrays);
        
    }
}
