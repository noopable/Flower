<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\Test;
/**
 * Description of TestTool
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class TestTool {
    
    public static function getPropertyRef($object, $name)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($name);
        $property->setAccessible(true);
        return $property;
    }
    
    public static function getPropertyValue($object, $name, $static = false)
    {
        $ref = self::getPropertyRef($object, $name);
        if ($static) {
            return $ref->getValue();
        } else {
            return $ref->getValue($object);
        }
    }
    
    public static function getMethodRef($object, $name)
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}
