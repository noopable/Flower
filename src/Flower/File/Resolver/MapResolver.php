<?php

namespace Flower\File\Resolver;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use ArrayIterator;
use IteratorAggregate;
use Traversable;
use Zend\Stdlib\ArrayUtils;

use Flower\Exception;

class MapResolver implements IteratorAggregate, ResolverInterface, ResolveTerminatorInterface
{
    use MayTerminateTrait;
    /**
     * @var array
     */
    protected $map = array();

    /**
     * Constructor
     *
     * Instantiate and optionally populate template map.
     *
     * @param  array|Traversable $map
     */
    public function __construct($map = array())
    {
        $this->setMap($map);
    }

    /**
     * IteratorAggregate: return internal iterator
     *
     * @return Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->map);
    }

    /**
     * Set (overwrite) template map
     *
     * Maps should be arrays or Traversable objects with name => path pairs
     *
     * @param  array|Traversable $map
     * @throws Exception\InvalidArgumentException
     * @return TemplateMapResolver
     */
    public function setMap($map)
    {
        if (!is_array($map) && !$map instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: expects an array or Traversable, received "%s"',
                __METHOD__,
                (is_object($map) ? get_class($map) : gettype($map))
            ));
        }

        if ($map instanceof Traversable) {
            $map = ArrayUtils::iteratorToArray($map);
        }

        $this->map = $map;
        return $this;
    }

    /**
     * Add an entry to the map
     *
     * @param  string|array|Traversable $nameOrMap
     * @param  null|string $path
     * @throws Exception\InvalidArgumentException
     * @return TemplateMapResolver
     */
    public function add($nameOrMap, $path = null)
    {
        if (is_array($nameOrMap) || $nameOrMap instanceof Traversable) {
            $this->merge($nameOrMap);
            return $this;
        }

        if (!is_string($nameOrMap)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: expects a string, array, or Traversable for the first argument; received "%s"',
                __METHOD__,
                (is_object($nameOrMap) ? get_class($nameOrMap) : gettype($nameOrMap))
            ));
        }

        if (empty($path)) {
            if (isset($this->map[$nameOrMap])) {
                unset($this->map[$nameOrMap]);
            }
            return $this;
        }

        $this->map[$nameOrMap] = $path;
        return $this;
    }

    /**
     * Merge internal map with provided map
     *
     * @param  array|Traversable $map
     * @throws Exception\InvalidArgumentException
     * @return TemplateMapResolver
     */
    public function merge($map)
    {
        if (!is_array($map) && !$map instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: expects an array or Traversable, received "%s"',
                __METHOD__,
                (is_object($map) ? get_class($map) : gettype($map))
            ));
        }

        if ($map instanceof Traversable) {
            $map = ArrayUtils::iteratorToArray($map);
        }

        //$this->map = array_replace_recursive($this->map, $map);
        /**
         * 配列を扱う
         * 2重に定義された場合両方がresolveされる
         */
        $this->map = array_merge_recursive($this->map, $map);
        return $this;
    }

    /**
     * Does the resolver contain an entry for the given name?
     *
     * @param  string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->map);
    }

    /**
     * Retrieve a template path by name
     *
     * @param  string $name
     * @return false|string
     * @throws Exception\DomainException if no entry exists
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            return false;
        }
        return $this->map[$name];
    }

    /**
     * Retrieve the template map
     *
     * @return array
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * Resolve a template/pattern name to a resource the renderer can consume
     *
     * @param  string $name
     * @return string
     */
    public function resolve($name, $spec = null)
    {
        if ($this->has($name)) {
            //map resolverはreadableをチェックしない。
            //FileInfoはファイルの実在を感知しない。
            $names = $this->get($name);
            if (is_string($names)) {
                $names = array($names);
            }
            
            if (!is_array($names)) {
                return null;
            }
            
            if (! count($names)) {
                return null;
            }
            
            foreach ($names as $name) {
                $file = new FileInfo($name);
                if (null === $spec) {
                    //すべて検索
                    $files[] = $file;
                    continue;
                }
                //キャッシュが使えるのでとにかくすべて検索
                if ($spec->isValid($file)) {
                    $files[] = $file;
                }
            }
        }
        return null;
    }
}
