<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Router;

use ArrayAccess;
use Flower\Resource\Exception\RuntimeException;
use Flower\Resource\Manager\StandardManager as ResourceManager;
use Traversable;
use Zend\Mvc\Router\RouteInterface;
use Zend\Mvc\Router\RoutePluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\Stdlib\ArrayUtils;
/**
 * Description of LazyLoadRoute
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class LazyLoadResourceRouteFactory implements FactoryInterface, MutableCreationOptionsInterface {

    protected $serviceLocator;

    protected $rootRouter;

    protected $resourceManager;

    protected $routePluginManager;

    protected $prototypes;

    protected $creationOptions;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof RoutePluginManager) {
            $this->routePluginManager = $serviceLocator;
            $this->serviceLocator = $this->routePluginManager->getServiceLocator();
        } else {
            $this->serviceLocator = $serviceLocator;
        }

        $options = $this->creationOptions;
        if (!isset($options)) {
            return null;
        }

        if (!isset($options['resource_id'])) {
            throw new RuntimeException('LazyLoadResourceRoute needs resource_id option');
        }

        if (!$resourceManager = $this->getResourceManager()) {
            return null;
        }

        if (!$resource = $resourceManager->get($options['resource_id'])) {
            return null;
        }

        $specs = $resource->getData();
        return $this->routeFromArray($specs);
    }

    /**
     * routeFromArray(): defined by SimpleRouteStack.
     *
     * @see    SimpleRouteStack::routeFromArray()
     * @param  string|array|Traversable $specs
     * @return RouteInterface
     * @throws Exception\InvalidArgumentException When route definition is not an array nor traversable
     * @throws Exception\InvalidArgumentException When chain routes are not an array nor traversable
     * @throws Exception\RuntimeException         When a generated routes does not implement the HTTP route interface
     */
    protected function routeFromArray($specs)
    {
        if (is_string($specs)) {
            if (null === ($route = $this->getPrototype($specs))) {
                throw new RuntimeException(sprintf('Could not find prototype with name %s', $specs));
            }
            return $route;
        } elseif ($specs instanceof Traversable) {
            $specs = ArrayUtils::iteratorToArray($specs);
        } elseif (!is_array($specs)) {
            throw new RuntimeException('Route definition must be an array or Traversable object');
        }

        $routePluginManager = $this->getRoutePluginManager();
        $prototypes = $this->getPrototypes();

        if (isset($specs['chain_routes'])) {
            if (!is_array($specs['chain_routes'])) {
                throw new RuntimeException('Chain routes must be an array or Traversable object');
            }

            $chainRoutes = array_merge(array($specs), $specs['chain_routes']);
            unset($chainRoutes[0]['chain_routes']);

            $options = array(
                'routes'        => $chainRoutes,
                'route_plugins' => $routePluginManager,
                'prototypes'    => $prototypes,
            );

            $route = $routePluginManager->get('chain', $options);
        } else {
            if ($specs instanceof Traversable) {
                $specs = ArrayUtils::iteratorToArray($specs);
            } elseif (!is_array($specs)) {
                throw new RuntimeException('Route definition must be an array or Traversable object');
            }

            if (!isset($specs['type'])) {
                throw new RuntimeException('Missing "type" option');
            } elseif (!isset($specs['options'])) {
                $specs['options'] = array();
            }

            try {
                $route = $routePluginManager->get($specs['type'], $specs['options']);
            } catch (\Zend\ServiceManager\Exception\ServiceNotFoundException $ex) {
                throw new RuntimeException('can\'t create from route spec', $ex->getCode(), $ex);
            }

            if (isset($specs['priority'])) {
                $route->priority = $specs['priority'];
            }
        }

        if (!$route instanceof RouteInterface) {
            throw new RuntimeException('Given route does not implement HTTP route interface');
        }

        if (isset($specs['child_routes'])) {
            $options = array(
                'route'         => $route,
                'may_terminate' => (isset($specs['may_terminate']) && $specs['may_terminate']),
                'child_routes'  => $specs['child_routes'],
                'route_plugins' => $routePluginManager,
                'prototypes'    => $prototypes,
            );

            $priority = (isset($route->priority) ? $route->priority : null);

            $route = $routePluginManager->get('part', $options);
            $route->priority = $priority;
        }

        return $route;
    }
    /**
     * Set creation options
     *
     * @param  array $options
     * @return void
     */
    public function setCreationOptions(array $options)
    {
        $this->creationOptions = $options;
    }

    public function setResourceManager(ResourceManager $resourceManager)
    {
        $this->resourceManager = $resourceManager;
    }

    public function getResourceManager()
    {
        return $this->resourceManager;
    }

    public function setRoutePluginManager(RoutePluginManager $routePluginManager = null)
    {
        if (null === $routePluginManager) {
            if (isset($this->serviceLocator) && $this->serviceLocator->has('RoutePluginManager')) {
                $routePluginManager = $this->serviceLocator->get('RoutePluginManager');
            }
        }
        $this->routePluginManager = $routePluginManager;
    }

    public function getRoutePluginManager()
    {
        if (!isset($this->routePluginManager)) {
            $this->setRoutePluginManager(null);
        }
        return $this->routePluginManager;
    }

    public function setRootRouter(RouteInterface $router = null)
    {
        if (null === $router) {
            if (isset($this->serviceLocator) && $this->serviceLocator->has('Router')) {
                $router = $this->serviceLocator->get('Router');
            }
        }
        $this->rootRouter = $router;
    }

    public function getRootRouter()
    {
        if (!isset($this->rootRouter)) {
            $this->setRootRouter(null);
        }
        return $this->rootRouter;
    }

    public function getPrototype($name)
    {
        if (! isset($this->prototypes)) {
            $this->setPrototypes(null);
        }

        if (!($this->prototypes instanceof ArrayAccess) || empty($this->prototypes)) {
            return null;
        }

        if (! isset($this->prototypes[$name])) {
            return null;
        }

        return $this->prototypes[$name];
    }

    public function setPrototypes($prototypes = null)
    {
        if (null === $prototypes) {
            if (($router = $this->getRootRouter())
                && method_exists($router, 'getPrototypes')) {
                $prototypes = $router->getPrototypes();
            }
        }
        $this->prototypes = $prototypes;
    }

    public function getPrototypes()
    {
        if (!isset($this->prototypes)) {
            $this->setPrototypes(null);
        }
        return $this->prototypes;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
