<?php
/**
 * Flower Module
 *  File
 *
 * copy and modification from Zend Framework Zend\View\Resolver\AggregateResolver
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 * @package   Flower_File
 */

namespace Flower\File\Resolver;

use Countable;
use IteratorAggregate;
use Zend\Stdlib\PriorityQueue;

use Flower\File\Resolver\ResolverInterface;
use Flower\File\FileInfo;

/**
 * @category   Flower
 * @package    Flower_File
 * @subpackage Resolver
 */
class AggregateResolver
 implements Countable, IteratorAggregate, ResolverInterface, ResolveTerminatorInterface
{
    use MayTerminateTrait;
    
    const FAILURE_NO_RESOLVERS = 'AggregateResolver_Failure_No_Resolvers';
    const FAILURE_NOT_FOUND    = 'AggregateResolver_Failure_Not_Found';

    /**
     * Last lookup failure
     * @var false|string
     */
    protected $lastLookupFailure = false;

    /**
     * @var Resolver
     */
    protected $lastSuccessfulResolver;

    /**
     * @var PriorityQueue
     */
    protected $queue;
    
    /**
     * Constructor
     *
     * Instantiate the internal priority queue
     *
     * @return void
     */
    public function __construct()
    {
        $this->queue = new PriorityQueue();
    }

    /**
     * Return count of attached resolvers
     *
     * @return void
     */
    public function count()
    {
        return $this->queue->count();
    }

    /**
     * IteratorAggregate: return internal iterator
     *
     * @return Traversable
     */
    public function getIterator()
    {
        return $this->queue;
    }

    /**
     * Attach a resolver
     *
     * @param  Resolver $resolver
     * @param  int $priority
     * @return AggregateResolver
     */
    public function attach(ResolverInterface $resolver, $priority = 1)
    {
        $this->queue->insert($resolver, $priority);
        return $this;
    }
    
    public function getResolvers()
    {
        return $this->queue;
    }

    /**
     * Resolve a template/pattern name to a resource the renderer can consume
     *
     * @param  string $name
     * @param  null|Callable $callback
     * @return false|string
     */
    public function resolve($name, $callback = null)
    {
        $this->lastLookupFailure      = false;
        $this->lastSuccessfulResolver = null;

        if (0 === count($this->queue)) {
            $this->lastLookupFailure = static::FAILURE_NO_RESOLVERS;
            return false;
        }

        $result = array();
        foreach ($this->queue as $resolver) {
            $resource = $resolver->resolve($name, $callback);
            if (!$resource) {
                // No resource found; try next resolver
                continue;
            }
            
            // Resource found;
            $this->lastSuccessfulResolver = $resolver;
            
            //return is path
            if (is_string($resource)) {
                $resource = new FileInfo($resource);
            }
            
            //FileInfo
            if (is_object($resource) && $resource instanceof FileInfo) {
                $resource = array($resource->getSpecifiedExtension() => $resource);
            }
            
            //multiple files 
            if (is_array($resource)) {
                $result = array_merge($result, $resource);
            }
            
            if ($resolver instanceof ResolveTerminatorInterface) {
                if (! $resolver->mayTerminate()) {
                    continue;
                }
            }
            return $result;
        }

        if (count($result) === 0) {
            $this->lastLookupFailure = static::FAILURE_NOT_FOUND;
            return false;
        }
        
        return $result;
    }

    /**
     * Return the last successful resolver, if any
     *
     * @return Resolver
     */
    public function getLastSuccessfulResolver()
    {
        return $this->lastSuccessfulResolver;
    }

    /**
     * Get last lookup failure
     *
     * @return false|string
     */
    public function getLastLookupFailure()
    {
        return $this->lastLookupFailure;
    }
}
