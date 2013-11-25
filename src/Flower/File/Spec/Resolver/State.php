<?php
namespace Flower\File\Spec\Resolver;
/*
 * 
 * 
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Flower\File\Spec\Resolver\ResolveSpecInterface;
use Flower\File\Spec\Cache\CacheSpecInterface;
/**
 * State Suffix付きメソッドが存在する場合はそれを呼ぶ。
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class State implements CacheSpecInterface, ResolveSpecInterface
{
    protected $spec;
    
    const READ  = '_read';
    const WRITE  = '_write';
    const ALL    = '_all';
    const CREATE = '_create';
    const CACHE  = '_cache';
    
    protected $states = ['_read', '_write', '_all', '_create', '_cache'];
    /**
     * method ベースでの書き換えを行う場合
     * configure時に追加する。
     * 
     * @var type 
     */
    protected $methods;
    
    protected $state;
    
    public function __construct($innerSpec, $state)
    {
        $this->spec = $innerSpec;
        if (in_array($state, $this->states)) {
            $this->state = $state;
        }
    }

    public function cacheEnabled()
    {
        return $this->delegateMethod(__FUNCTION__, func_get_args());
    }

    public function configure()
    {
        //parent spec must have already configured
    }

    public function getCacheExtension()
    {
        return $this->delegateMethod(__FUNCTION__, func_get_args());
    }

    public function getCacheFileName($name)
    {
        return $this->delegateMethod(__FUNCTION__, func_get_args());
    }

    public function getCachePath()
    {
        return $this->delegateMethod(__FUNCTION__, func_get_args());
    }

    public function getExtensions()
    {
        return $this->delegateMethod(__FUNCTION__, func_get_args());
    }

    public function getGlobPattern($name)
    {
        return $this->delegateMethod(__FUNCTION__, func_get_args());
    }

    public function getMap()
    {
        return $this->delegateMethod(__FUNCTION__, func_get_args());
    }

    public function getPathStack()
    {
        return $this->delegateMethod(__FUNCTION__, func_get_args());
    }
    
    public function getResolver()
    {
        return $this->delegateMethod(__FUNCTION__, func_get_args());
    }

    public function isValid(\Flower\File\FileInfo $fileInfo)
    {
        return $this->delegateMethod(__FUNCTION__, func_get_args());
    }
    
    public function __call($name, $args)
    {
        return $this->delegateMethod($name, $args);
    }
    
    protected function delegateMethod($function, $args)
    {
        if (isset($this->methods[$function])) {
            $method = $this->methods[$function];
            array_unshift($args, $this->spec);
            return call_user_func_array($method, $args);
        }
        elseif (isset($this->state) && method_exists($this->spec, $function . $this->state)) {
            return call_user_func_array([$this->spec, $function . $this->state], $args);
        }
        else {
            return call_user_func_array([$this->spec, $function], $args);
        }
    }

    public function setResolver(\Flower\File\Resolver\ResolverInterface $resolver)
    {
        
    }
}
