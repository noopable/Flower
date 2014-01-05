<?php
namespace Flower\View;
/*
 * 
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Traversable;

use Zend\Stdlib\ArrayUtils;

/**
 * sprintf関数を構成してビューを組み立てる
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class Sprintf {
    
    protected $format = '';
    
    protected $params = array();
    
    public function __construct($format = null)
    {
        $args = func_get_args();
        
        if (null !== $format) {
            $this->setFormat($format);
        }
        
        if (count($args) > 1) {
            //drop format
            array_shift($args);
            $this->setParams($args);
        }
    }
    
    public static function factoryFromArray(array $config)
    {
        if (count($config)) {
            $instance = new static;
            $format = array_shift($config);
            if (! static::isStringConversive($format)) {
                return '';
            }
            $instance->setFormat($format);
            
            foreach ($config as $param) {
                if (is_array($param)) {
                    $param = static::factoryFromArray($param);
                }
                if (static::isScalarConversive($param)) {
                    $params[] = $param;
                }
                else {
                    $params[] = '';
                }
            }
            
            if (isset($params)) {
                $instance->setParams($params);
            }
            
            return $instance;
        }
    }
    
    public function setFormat($format)
    {
        //セット段階ではstringでなくてもよい。
        if ($this->isStringConversive($format)) {
            $this->format = $format;
        }
    }
    
    public static function isStringConversive($string)
    {
        return is_string($string) || is_object($string) && method_exists($string, '__toString');
    }
    
    public static function isScalarConversive($scalar)
    {
        return is_scalar($scalar) || static::isStringConversive($scalar) || is_callable($scalar);
    }
    
    public function addParam($param, $key = null, $type = 'string')
    {
        //スカラーかコールバックってことにするか。
        //__toString
        if ($this->isScalarConversive($param)) {
            if (null === $key) {
                $this->params[] = $param;
            }
            else {
                $this->params[(string) $key] = $param;
            }
        }
    }
    
    public function setParams(array $params)
    {
        $this->params = $params;
    }
    
    /**
     * 変数のオーダーが事実上重要であるので非推奨
     * 
     * @param type $mergeParams
     */
    public function mergeParams($mergeParams)
    {
        if (is_object($mergeParams) && $mergeParams instanceof Traversable) {
            $mergeParams = ArrayUtils::iteratorToArray($mergeParams);
        }
        
        $this->params = array_merge($this->params, $mergeParams);
    }
            
    public function __toString()
    {
        $params = array((string) $this->format);
        foreach ($this->params as $param) {
            if ($this->isStringConversive($param)) {
                $params[] = (string) $param;
            }
            elseif (is_callable($param)) {
                $params[] = $param($this);
            }
            else {
                $params[] = $param;
            }
        }
        $res = call_user_func_array('sprintf', $params);
        if (is_string($res)) {
            return $res;
        }
        else {
            return '';
        }
    }
}
