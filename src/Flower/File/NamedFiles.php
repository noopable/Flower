<?php
namespace Flower\File;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use IteratorAggregate;
use ArrayIterator;
use Countable;
/**
 * Description of Named
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class NamedFiles implements IteratorAggregate, Countable {

    protected $name;

    /**
     * key = extension
     * @var FileInfo[]
     */
    protected $files;

    public function __construct($name, array $files = array())
    {
        $this->name = $name;
        foreach ($files as $fileInfo) {
            if (is_object($fileInfo) && $fileInfo instanceof FileInfo) {
                $this->setFile($fileInfo);
            }
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function setFile(FileInfo $fileInfo, $specifiedExtension = null)
    {
        if ($specifiedExtension) {
            $fileInfo->setSpecifiedExtension($specifiedExtension);
        }
        else {
            $specifiedExtension = $fileInfo->getExtension();
        }

        $this->files[$specifiedExtension] = $fileInfo;
    }

    public function getFile($specifiedExtension = null, $first = true)
    {
        if (null === $specifiedExtension) {
            if ($first) {
                return reset($this->files);
            }
            else {
                return end($this->files);
            }
        }

        if (isset($this->files[$specifiedExtension])) {
            return $this->files[$specifiedExtension];
        }
    }

    public function clearFiles()
    {
        $this->files = array();
    }

    /**
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        $files = $this->files;
        reset($files);
        return new ArrayIterator($files);
    }

    public function count() {
        return count($this->files);
    }
}
