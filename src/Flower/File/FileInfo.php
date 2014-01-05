<?php
namespace Flower\File;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use SplFileInfo;
/**
 * Description of FileInfo
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class FileInfo extends SplFileInfo {
    
    protected $specifiedExtension;
    
    protected $value;
    
    public function setSpecifiedExtension($specifiedExtension)
    {
        $this->specifiedExtension = $specifiedExtension;
    }
    
    public function getSpecifiedExtension()
    {
        if (isset($this->specifiedExtension)) {
            return $this->specifiedExtension;
        }
        else {
            return $this->getExtension();
        }
    }
    
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    public function getValue()
    {
        return $this->value;
    }
}
