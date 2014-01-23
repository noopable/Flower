<?php
namespace FlowerTest\Router;

use Flower\Router\LazyLoadFileRouteFactory;
use Flower\Router\TreeRouteStack;
use Flower\Test\TestTool;
use Zend\Mvc\Router\RoutePluginManager;
use Zend\ServiceManager\ServiceManager;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-01-23 at 22:31:27.
 */
class LazyLoadFileRouteFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LazyLoadFileRouteFactory
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

        $this->object = new LazyLoadFileRouteFactory;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\Router\LazyLoadFileRouteFactory::createService
     */
    public function testCreateServiceWithStandardUse()
    {
        $name = 'foo';
        $creationOptions = array(
            'name' => $name,
        );
        $routeConf = array(
            'type' => 'Zend\Mvc\Router\Http\Literal',
            'options' => array('route' => 'Foo'),
        );
        $this->object->setCreationOptions($creationOptions);
                //リソースを取得するためのオブジェクト
        $fileService = $this->getMock('Flower\File\Gateway\GatewayInterface');
        $fileService->expects($this->once())
                ->method('read')
                ->with($this->equalTo($name))
                ->will($this->returnValue($routeConf));
        $this->object->setFileService($fileService);
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
     * @covers Flower\Router\LazyLoadFileRouteFactory::setFileService
     */
    public function testSetFileService()
    {
        $fileService = $this->getMock('Flower\File\Gateway\GatewayInterface');
        $this->object->setFileService($fileService);
        $this->assertSame($fileService, TestTool::getPropertyValue($this->object, 'fileService'));
    }

    /**
     * @covers Flower\Router\LazyLoadFileRouteFactory::getFileService
     */
    public function testGetFileService()
    {
        $fileService = $this->getMock('Flower\File\Gateway\GatewayInterface');
        $this->object->setFileService($fileService);
        $this->assertSame($fileService, $this->object->getFileService());
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

    /**
     * @covers Flower\Router\LazyLoadFileRouteFactory::setServiceLocator
     */
    public function testSetServiceLocator()
    {
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->object->setServiceLocator($serviceLocator);
        $this->assertSame($serviceLocator, TestTool::getPropertyValue($this->object, 'serviceLocator'));
    }

    /**
     * @covers Flower\Router\LazyLoadFileRouteFactory::getServiceLocator
     */
    public function testGetServiceLocator()
    {
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->object->setServiceLocator($serviceLocator);
        $this->assertSame($serviceLocator, $this->object->getServiceLocator());
    }
}
