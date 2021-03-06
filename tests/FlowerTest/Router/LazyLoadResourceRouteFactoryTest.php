<?php
namespace FlowerTest\Resource\Route;

use Flower\Router\LazyLoadResourceRouteFactory;
use Flower\Router\TreeRouteStack;
use Flower\Test\TestTool;
use Zend\Mvc\Router\RoutePluginManager;
use Zend\ServiceManager\ServiceManager;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-01-23 at 17:46:32.
 */
class LazyLoadResourceRouteFactoryTest extends \PHPUnit_Framework_TestCase
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
        //setup
        $this->serviceLocator = new ServiceManager();
        $this->routePluginManager = $routePluginManager = new RoutePluginManager;
        $routePluginManager->setServiceLocator($this->serviceLocator);
        $this->serviceLocator->setService('RoutePluginManager', $routePluginManager);
        // @see Zend\Mvc\Service\RouterFactory
        $rootRouter = TreeRouteStack::factory(array('route_plugins' => $routePluginManager));
        $this->serviceLocator->setService('Router', $rootRouter);

        $this->object = new LazyLoadResourceRouteFactory;

    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\Router\LazyLoadResourceRouteFactory::createService
     */
    public function testCreateServiceStandardUse()
    {
        $resourceId = 'foo';
        $creationOptions = array(
            'resource_id' => $resourceId,
        );
        $routeConf = array(
            'type' => 'Zend\Mvc\Router\Http\Literal',
            'options' => array('route' => 'Foo'),
        );
        $resource = $this->getMock('Flower\Resource\ResourceClass\ResourceInterface');
        $resource->expects($this->once())
                ->method('getData')
                ->will($this->returnValue($routeConf));
        $this->object->setCreationOptions($creationOptions);
                //リソースを取得するためのオブジェクト
        $resourceManager = $this->getMock('Flower\Resource\Manager\StandardManager');
        $resourceManager->expects($this->once())
                ->method('get')
                ->with($this->equalTo($resourceId))
                ->will($this->returnValue($resource));
        $this->object->setResourceManager($resourceManager);
        $service = $this->object->createService($this->routePluginManager);

        $this->assertInstanceOf('Zend\Mvc\Router\Http\Literal', $service);
    }

    /**
     *
     * @expectedException Flower\Resource\Exception\RuntimeException
     */
    public function testCreateServiceWithInvalidData()
    {
        $resourceId = 'foo';
        $creationOptions = array(
            'resource_id' => $resourceId,
        );
        $routeConf = array(
            'type' => 'nonexists',
            'options' => array('route' => 'Foo'),
        );
        $resource = $this->getMock('Flower\Resource\ResourceClass\ResourceInterface');
        $resource->expects($this->once())
                ->method('getData')
                ->will($this->returnValue($routeConf));
        $this->object->setCreationOptions($creationOptions);
                //リソースを取得するためのオブジェクト
        $resourceManager = $this->getMock('Flower\Resource\Manager\StandardManager');
        $resourceManager->expects($this->once())
                ->method('get')
                ->with($this->equalTo($resourceId))
                ->will($this->returnValue($resource));
        $this->object->setResourceManager($resourceManager);
        $service = $this->object->createService($this->routePluginManager);

        $this->assertInstanceOf('Zend\Mvc\Router\Http\Literal', $service);
    }

    /**
     * @covers Flower\Router\LazyLoadResourceRouteFactory::setCreationOptions
     */
    public function testSetCreationOptions()
    {
        $creationOptions = array(
                'type' => 'literal',
                'options' => array('route' => 'Foo'),
        );
        $this->object->setCreationOptions($creationOptions);
        $this->assertEquals($creationOptions, TestTool::getPropertyValue($this->object, 'creationOptions'));
    }

    /**
     * @covers Flower\Router\LazyLoadResourceRouteFactory::setResourceManager
     */
    public function testSetResourceManager()
    {
        $resourceManager = $this->getMock('Flower\Resource\Manager\StandardManager');
        $this->object->setResourceManager($resourceManager);
        $this->assertSame($resourceManager, TestTool::getPropertyValue($this->object, 'resourceManager'));
    }

    /**
     * @covers Flower\Router\LazyLoadResourceRouteFactory::getResourceManager
     */
    public function testGetResourceManager()
    {
        $this->assertNull($this->object->getResourceManager());
        $resourceManager = $this->getMock('Flower\Resource\Manager\StandardManager');
        $this->object->setResourceManager($resourceManager);
        $this->assertSame($resourceManager, $this->object->getResourceManager());
    }

    /**
     * @covers Flower\Router\LazyLoadResourceRouteFactory::setRoutePluginManager
     */
    public function testSetRoutePluginManager()
    {
        $this->assertNull(TestTool::getPropertyValue($this->object, 'routePluginManager'));
        //without settings
        $this->object->setRoutePluginManager();
        $this->assertNull(TestTool::getPropertyValue($this->object, 'routePluginManager'));
        //having serviceLocator
        $this->object->setServiceLocator($this->serviceLocator);
        $this->object->setRoutePluginManager();
        $prop1 = TestTool::getPropertyValue($this->object, 'routePluginManager');
        $this->assertInstanceOf('Zend\Mvc\Router\RoutePluginManager', $prop1);

        $routePluginManager = new RoutePluginManager;
        $this->object->setRoutePluginManager($routePluginManager);
        $prop2 = TestTool::getPropertyValue($this->object, 'routePluginManager');
        $this->assertSame($routePluginManager, $prop2);
        $this->assertNotSame($prop1, $prop2);
    }

    /**
     * @covers Flower\Router\LazyLoadResourceRouteFactory::getRoutePluginManager
     */
    public function testGetRoutePluginManager()
    {
        $routePluginManager = new RoutePluginManager;
        $this->object->setRoutePluginManager($routePluginManager);
        $this->assertSame($routePluginManager, $this->object->getRoutePluginManager());
    }

    /**
     * @covers Flower\Router\LazyLoadResourceRouteFactory::setRootRouter
     */
    public function testSetRootRouter()
    {
        $this->assertNull(TestTool::getPropertyValue($this->object, 'rootRouter'));
        //without settings
        $this->object->setRootRouter();
        $this->assertNull(TestTool::getPropertyValue($this->object, 'rootRouter'));
        //having serviceLocator
        $this->object->setServiceLocator($this->serviceLocator);
        $this->object->setRootRouter();
        $prop1 = TestTool::getPropertyValue($this->object, 'rootRouter');
        $this->assertInstanceOf('Flower\Router\TreeRouteStack', $prop1);
    }

    /**
     * @covers Flower\Router\LazyLoadResourceRouteFactory::getRootRouter
     */
    public function testGetRootRouter()
    {
        $this->assertNull($this->object->getRootRouter());
        $this->object->setServiceLocator($this->serviceLocator);
        $this->assertInstanceOf('Flower\Router\TreeRouteStack', $this->object->getRootRouter());
    }

    /**
     * @covers Flower\Router\LazyLoadResourceRouteFactory::setPrototypes
     */
    public function testSetPrototypes()
    {
        $prototypes = array(
            'foo' => array(
                'type' => 'literal',
                'options' => array('route' => 'Foo'),
            ),
            'bar' => array(
                'type' => 'segment',
                'options' => array('route' => 'blog'),
            ),
        );
        $this->object->setServiceLocator($this->serviceLocator);
        $router = $this->object->getRootRouter();
        $router->addPrototypes($prototypes);

        $this->object->setPrototypes(null);
        $prop1 = TestTool::getPropertyValue($this->object, 'prototypes');
        $this->assertInstanceOf('ArrayObject', $prop1);
        $resArray = $prop1->getArrayCopy();
        $this->assertArrayHasKey('foo', $resArray );
        $this->assertArrayHasKey('bar', $resArray );
        $this->assertInstanceOf('Zend\Mvc\Router\Http\Literal', $prop1['foo']);
        $this->assertInstanceOf('Zend\Mvc\Router\Http\Segment', $prop1['bar']);
    }

    /**
     * @covers Flower\Router\LazyLoadResourceRouteFactory::getPrototypes
     */
    public function testGetPrototypes()
    {
        $prototypes = array(
            'foo' => array(
                'type' => 'literal',
                'options' => array('route' => 'Foo'),
            ),
            'bar' => array(
                'type' => 'segment',
                'options' => array('route' => 'blog'),
            ),
        );
        $this->object->setPrototypes($prototypes);
        $this->assertEquals($prototypes, $this->object->getPrototypes());
    }

    /**
     * @covers Flower\Router\LazyLoadResourceRouteFactory::getPrototype
     */
    public function testGetPrototype()
    {
        $prototypes = array(
            'foo' => array(
                'type' => 'literal',
                'options' => array('route' => 'Foo'),
            ),
            'bar' => array(
                'type' => 'segment',
                'options' => array('route' => 'blog'),
            ),
        );
        $this->object->setServiceLocator($this->serviceLocator);
        $router = $this->object->getRootRouter();
        $router->addPrototypes($prototypes);

        $this->object->setPrototypes(null);
        $this->assertInstanceOf('Zend\Mvc\Router\Http\Literal', $this->object->getPrototype('foo'));
    }
}
