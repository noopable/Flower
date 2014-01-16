<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\ServiceLayer\Wrapper;

use Flower\ServiceLayer\ServiceLayerInterface;
/**
 * Description of AbstractProxy
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
abstract class AbstractProxy implements ServiceLayerInterface {
    
    protected $innerObject;
    
    public function __construct($instance = null)
    {
        $this->innerObject = $instance;
    }
   
    public function passThrough()
    {
        return $this->innerObject;
    }
    
    /**
     * do something
     * ex.
     * return call_user_func_array(array($this->innerObject, $name), $arguments);
     */
    abstract public function proxy($name, $arguments);
            
    /**
     * 
     * @param type $name
     * @param type $arguments
     * @return type
     * @throws RuntimeException
     */
    public function __call($name, $arguments)
    {
        return $this->proxy($name, $arguments);
    }
}
