<?php
namespace Flower\File\Spec\Resolver;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Flower\File\Spec\Resolver\ResolveSpecInterface;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;


use Flower\File\Resolver\ResolverInterface;

use Flower\File\Event;
use Flower\File\Resolver;
use Flower\File\FileInfo;

/**
 * Description of TreeArrayMerge
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class Tree implements ResolveSpecInterface, ListenerAggregateInterface
{
    protected $config;

    protected $extensions = array(
        'ini'  => 'ini',
        'json' => 'json',
        'xml'  => 'xml',
        'yaml' => 'yaml',
        'php' => 'php',
    );

    protected $defaultExtension = 'default.php';

    protected $extensionToWrite;

    protected $map;

    protected $pathStack;

    protected $resolver;

    protected $cachePath;

    protected $cacheEnabled;

    protected $cacheResolver;

    public function __construct(array $config = null)
    {
        if (null !== $config) {
            $this->config =$config;
        }
    }

    public function configure()
    {
        $config = $this->config;

        if (isset($config['map'])) {
            $this->map = $config['map'];
        }

        if (isset($config['path_stack'])) {
            $this->pathStack = $config['path_stack'];
        }

        if (isset($config['extensions'])) {
            $this->extensions = $config['extensions'];
        }
        
        if (isset($config['default_extension'])) {
            $this->defaultExtension = $config['default_extension'];
        }

        if (!in_array($this->defaultExtension, $this->extensions)) {
            array_unshift($this->extensions, $this->defaultExtension);
        }
    }

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(Event::EVENT_RESOLVE, [$this, 'onResolve']);
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function setResolver(ResolverInterface $resolver = null)
    {
        if (null !== $resolver) {
            $this->resolver = $resolver;
            return;
        }

        if (isset($this->resolver)) {
            return;
        }

        /**
         * set DefaultResolver
         */
        $this->resolver = new Resolver\DefaultResolver;
        if ($map = $this->getMap()) {
            $mapResolver =  new Resolver\MapResolver($map);
            $this->resolver->attach($mapResolver);
        }

        if ($pathStack = $this->getPathStack()) {
            //TODO: get glob setting and create for it
            $stackResolver = new Resolver\PathStack();
            $stackResolver->setDefaultSuffix($this->defaultExtension);
            $stackResolver->addPaths($pathStack);
            $this->resolver->attach($stackResolver);
        }
    }

    public function getResolver()
    {
        if (!isset($this->resolver)) {
            $this->setResolver();
        }
        return $this->resolver;
    }

    public function getExtensions()
    {
        return $this->extensions;
    }

    public function getExtensions_create()
    {
        if (isset($this->extensionToWrite)) {
            return [$this->extensionToWrite];
        }
        return $this->extensions;
    }

    public function getExtensions_write()
    {
        if (isset($this->extensionToWrite)) {
            return [$this->extensionToWrite];
        }
        return $this->extensions;
    }

    public function getMap()
    {
        return $this->map;
    }

    public function getPathStack()
    {
        return $this->pathStack;
    }

    public function isValid (FileInfo $fileInfo)
    {
        return ($fileInfo->isFile() && $fileInfo->isReadable());
    }

    public function isValid_write (FileInfo $fileInfo)
    {
        return ($fileInfo->isFile() && $fileInfo->isWritable());
    }

    public function isValid_all (FileInfo $fileInfo)
    {
        return true;
    }

    public function isValid_create (FileInfo $fileInfo)
    {
        $parent = $fileInfo->getPathInfo();
        if ($parent->isDir()) {
            return ($parent->isWritable());
        }

        $pathStack = $this->getPathStack();
        $path = $parent->getPathname();
        foreach ($pathStack as $basepath) {
            if (strpos($path, $basepath) === 0) {
                return true;
            }
        }
        return false;
    }

    public function isValid_read (FileInfo $fileInfo)
    {
        return ($fileInfo->isFile() && $fileInfo->isReadable());
    }

    public function onResolve(Event $e)
    {
        /* @var $namedFiles NamedFiles */
        $namedFiles = $e->getNamedFiles();
        $name = $namedFiles->getName();
        $resolveMode = $e->getResolveMode();

        switch ($resolveMode) {
            case Event::RESOLVE_READ:
                $state = State::READ;
                break;
            case Event::RESOLVE_ALL:
                $state = State::ALL;
                break;
            case Event::RESOLVE_WRITE:
                $state = State::WRITE;
                $this->extensionToWrite = $e->getParam('extension', $this->defaultExtension);
                break;
            case Event::RESOLVE_CREATE:
                $state = State::CREATE;
                $this->extensionToWrite = $e->getParam('extension', $this->defaultExtension);
                break;
            default:
                $state = State::READ;
                break;
        }

        $spec = new State($this, $state);
        $res = $this->getResolver()->resolve($name, $spec);
        if ($resolveMode === Event::RESOLVE_WRITE || $resolveMode === Event::RESOLVE_CREATE) {
            $this->extensionToWrite = null;
        }

        if ($res) {
            if (!is_array($res)) {
                if (is_string($res)) {
                    $e->addState($name . ': resolver returns ' . $res);
                }
                $res = array($res);
            }

            foreach ($res as $k => $v) {
                if (is_string($v)) {
                    $v = new FileInfo($v);
                }

                if ($v instanceof FileInfo) {
                    $namedFiles->setFile($v, $v->getSpecifiedExtension());
                }
            }

            return $namedFiles;
        }

        // not resolved
        $e->addState($name . ': resolver returns null');
        return null;
    }

}
