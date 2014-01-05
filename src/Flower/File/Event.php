<?php
namespace Flower\File;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\EventManager\Event as AbstractEvent;
/**
 * Description of Event
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class Event extends AbstractEvent{
    
    /**
     * event names
     */
    const EVENT_RESOLVE = 'resolve';
    const EVENT_READ = 'read';
    const EVENT_WRITE = 'write';
    const EVENT_REFRESH = 'refresh';
    
    /**
     * inner event names
     */
    const EVENT_MERGE = 'merge';
    const EVENT_FILE_READ = 'file_read';
    const EVENT_FILE_WRITE = 'file_write';
    const EVENT_CACHE_READ = 'cache_read';
    const EVENT_CACHE_MAKE = 'cache_make';
        
    /**
     * resolve modes
     */
    const RESOLVE_READ = 'resolve_read';
    const RESOLVE_WRITE = 'resolve_write';
    const RESOLVE_CREATE = 'resolve_create';
    const RESOLVE_ALL = 'resolve_all';
        
    /**
     * my event name
     * @var string
     */
    protected $name;
    
    /**
     *
     * @var NamedFiles
     */
    protected $namedFiles;
    
    /**
     * target data
     * 
     * @var mixed
     */
    protected $data;
    
    protected $resolveMode;
    
    protected $states = array();
    
    public function getFiles()
    {
        return $this->getNamedFiles();
    }
    
    public function setDataName($dataName)
    {
        $this->dataName = $dataName;
    }
    
    public function getDataName()
    {
        return $this->dataName;
    }
    
    public function getData()
    {
        return $this->data;
    }
    
    public function setData($data)
    {
        $this->data = $data;
    }
    
    /**
     * 
     * @return NamedFiles
     * @throws Exception\RuntimeException
     */
    public function getNamedFiles()
    {
        if (! isset($this->namedFiles)) {
            if (!isset($this->dataName)) {
                throw new Exception\RuntimeException(__CLASS__ . ' needs data target name');
            }
            $this->namedFiles = new NamedFiles($this->dataName);
        }
        return $this->namedFiles;
    }
    
    public function setResolveMode($mode)
    {
        $this->resolveMode = $mode;
    }
    
    public function getResolveMode()
    {
        return $this->resolveMode;
    }
    
    public function getStates()
    {
        return $this->states;
    }
    
    public function addState($state)
    {
        $this->states[] = $state;
    }
            
}
