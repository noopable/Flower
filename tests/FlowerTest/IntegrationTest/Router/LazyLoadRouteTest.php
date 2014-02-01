<?php
namespace FlowerTest\IntegrationTest\Router;

use Flower\Resource\Route\LazyLoadResourceRouteFactory;
use Flower\Test\TestTool;
use FlowerTest\IntegrationTest\TestAsset\ServiceLocator;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-01-23 at 17:46:32.
 */
class LazyLoadRouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LazyLoadResourceRouteFactory
     */
    protected $object;

    protected $serviceLocator;

    protected $routePluginManager;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $config = include __DIR__ . '/TestAsset/standard.config.php';
        $this->serviceLocator = $sl = ServiceLocator::getServiceLocator($config);
        //$sl->setService('Router', 'Zend\Mvc\Service\RouterFactory');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $fileService = $this->serviceLocator->get('Flower_LazyRouteLoaderFile');
        $fileService->refresh();
    }

    public function testStandardServiceLocator()
    {
        $this->assertInstanceOf('Zend\ServiceManager\ServiceManager', $this->serviceLocator);
    }

    public function testServiceLocatorHasRoutePluginManager()
    {
        $routePluginManager = $this->serviceLocator->get('RoutePluginManager');
        $this->assertInstanceOf('Zend\Mvc\Router\RoutePluginManager', $routePluginManager);
    }

    public function testRoutePluginHasInvokables()
    {
        $routePluginManager = $this->serviceLocator->get('RoutePluginManager');
        $this->assertArrayNotHasKey('chain',
                TestTool::getPropertyValue($routePluginManager, 'invokableClasses'),
                'has no invokables because Route has not instantiated yet.');
    }

    public function testRouterClass()
    {
        $this->assertFalse(\Zend\Console\Console::isConsole(), 'be sure not Console mode');
        $this->assertInstanceOf('Flower\Router\TreeRouteStack', $this->serviceLocator->get('Router'));
    }

    public function testRequest()
    {
        $request = $this->serviceLocator->get('Request');
        $this->assertInstanceOf('Zend\Stdlib\RequestInterface', $request);
        $this->assertInstanceOf('Zend\Http\Request', $request);
    }

    public function testTargetRoute()
    {
        $router = $this->serviceLocator->get('Router');
        $route = $router->getRoute('second');
        $this->assertInstanceOf('Zend\Mvc\Router\Http\Part', $route);
        $childRoutes = TestTool::getPropertyValue($route, 'childRoutes');
        $this->assertArrayHasKey('file', $childRoutes);
        $this->assertEquals('lazyfile', $childRoutes['file']['type']);
        $request = $this->serviceLocator->get('Request');
        $request->setUri('http://example.com/foo/bar');
        $routeMatch = $router->match($request);
        $fileRoute = $route->getRoute('file');
        $this->assertInstanceOf('Zend\Mvc\Router\Http\Literal', $fileRoute);
    }

    public function testMatchFirst()
    {
        $router = $this->serviceLocator->get('Router');
        $request = $this->serviceLocator->get('Request');
        $request->setUri('http://example.com/abc');
        $routeMatch = $router->match($request);
        $this->assertInstanceOf('Zend\Mvc\Router\RouteMatch', $routeMatch);
        $this->assertEquals('first', $routeMatch->getMatchedRouteName());
    }

    public function testMatchSecond()
    {
        $router = $this->serviceLocator->get('Router');
        $request = $this->serviceLocator->get('Request');
        $request->setUri('http://example.com/foo/bar');
        $routeMatch = $router->match($request);
        $this->assertInstanceOf('Zend\Mvc\Router\RouteMatch', $routeMatch);
        $this->assertEquals('second/file', $routeMatch->getMatchedRouteName());
    }

}
