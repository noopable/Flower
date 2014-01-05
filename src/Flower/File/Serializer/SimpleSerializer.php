<?php
namespace Flower\File\Serializer;
/*
 * 
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

/**
 * Description of Serializer
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class SimpleSerializer implements SerializerInterface
{

    public function serialize($data)
    {
        return serialize($data);
    }

    public function test($data)
    {
        try {
            set_error_handler(array($this, 'handleError'));
            $serialized = $this->serialize($data);
            $res = $this->unserialize($serialized);
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage, E_USER_NOTICE);
            restore_error_handler();
            return false;
        }
        restore_error_handler();
        return true;
    }

    public function unserialize($serialized)
    {
        return unserialize($serialized);
    }
    
    public function handleError($errno, $errstr, $errfile, $errline)
    {
        throw new \Exception($errno . ': ' . $errstr . ' in ' . $errfile . ' : ' . $errline);
    }
}
