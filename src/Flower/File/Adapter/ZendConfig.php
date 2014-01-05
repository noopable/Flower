<?php
namespace Flower\File\Adapter;
/*
 * 
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\Config\Factory;
use Zend\Config\Exception\RuntimeException;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

use Flower\File\FileInfo;
use Flower\File\Event;
/**
 * Description of ZendConfig
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ZendConfig implements AdapterInterface, ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();
    
    protected $breakOnFailure = false;
    
    public function __construct(array $config = null)
    {
        if (is_array($config)) {
            $this->config = $config;
        }
    }
    
    public function configure()
    {
        if (isset($config) && isset($config['break_on_failure'])) {
            $this->breakOnFailure = (boolean) $config['break_on_failure'];
        }
    }
    
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(Event::EVENT_FILE_READ, [$this, 'onRead']);
        $this->listeners[] = $events->attach(Event::EVENT_FILE_WRITE, [$this, 'onWrite']);
    }
    
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }
    /**
     * 
     * @param \Flower\File\NamedFiles $e
     */
    public function onRead(Event $e)
    {
        
        $files = $e->getNamedFiles();
        $count = 0;
        
        foreach ($files as $file)
        {
            if ($file->isFile() && $file->isReadable()) {
                $realpath = $file->getRealPath();
                try {
                    $data = Factory::fromFile($realpath);
                    $file->setValue($data);
                    $count++;
                } 
                catch (RuntimeException $e) {
                    if ($this->breakOnFailure) {         
                        throw $e;
                    }
                    trigger_error('skip file(' . $realpath . ') because:' . $e->getMessage(), E_USER_WARNING);
                }
            }
        }
        
        return $files;
    }
    
    public function onWrite(Event $e)
    {
        $files = $e->getNamedFiles();
        $count = 0;
        foreach ($files as $fileInfo) {
            if ($fileInfo instanceof FileInfo) {
                $filename = $fileInfo->getPathname();
                $parent = $fileInfo->getPathInfo();
                if (!file_exists($parent->getPathname())) {
                    mkdir($parent->getPathname());
                }
                
                $data = $fileInfo->getValue();
                $count++;
                Factory::toFile($filename, $data);
            }
        }
        return $count;
    }
    
}
