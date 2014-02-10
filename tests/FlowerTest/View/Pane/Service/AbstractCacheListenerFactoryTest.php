<?php
namespace FlowerTest\View\Pane\Service;

use Flower\Domain\Service as DomainService;
use Flower\Test\TestTool;
use Zend\Serializer\Adapter\PhpSerialize;
use Zend\ServiceManager\ServiceManager;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-10 at 19:57:23.
 */
class AbstractCacheListenerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractCacheListenerFactory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new TestAsset\ConcreteCacheListenerFactory;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\View\Pane\Service\AbstractCacheListenerFactory::createService
     */
    public function testCreateServiceSimple()
    {
        $config = array(
            TestTool::getPropertyValue($this->object, 'configKey') => array(),
        );
        $serviceLocator = new ServiceManager;
        $serviceLocator->setService('Config', $config);
        $res = $this->object->createService($serviceLocator);
        //defaultListenerClass
        $this->assertInstanceOf(TestTool::getPropertyValue($this->object, 'defaultListenerClass'), $res);
    }

    public function testCreateListenerSimple()
    {
        $config = array(
            TestTool::getPropertyValue($this->object, 'configKey') => array(
                'listener_class' => 'Flower\View\Pane\ManagerListener\PaneCacheListener',
                'cache_storage' => array(
                    'adapter' => array(
                        'name'    => 'filesystem',
                        'options' => array(
                            'namespace' => 'test_pane_manager',
                            'cache_dir' => 'TestAsset/tmp/cache',
                            'dir_level' => 1,
                        ),
                    ),
                    'plugins' => array(
                        'exception_handler' => array('throw_exceptions' => true),
                    ),
                ),
                'domain_service' => 'TestDomainService',
            ),
        );
        $serviceLocator = new ServiceManager;
        $res = $this->object->createListener($serviceLocator, $config);
        $this->assertInstanceOf('Flower\View\Pane\ManagerListener\PaneCacheListener', $res);
    }

    /**
     * @covers Flower\View\Pane\Service\AbstractCacheListenerFactory::configureCacheStorage
     */
    public function testConfigureCacheStorageWithObject()
    {
        $listener = $this->getMock('Flower\View\Pane\ManagerListener\CacheListenerInterface');
        $storage = $this->getMock('Zend\Cache\Storage\StorageInterface');
        $listener->expects($this->once())
                ->method('setStorage')
                ->with($this->equalTo($storage));

        $this->object->configureCacheStorage($listener, $storage);
    }

    public function testConfigureCacheStorageWithArray()
    {
        $listener = $this->getMock('Flower\View\Pane\ManagerListener\CacheListenerInterface');
        $storage = array('foo' => 'bar');
        $listener->expects($this->once())
                ->method('setStorageOptions')
                ->with($this->equalTo($storage));

        $this->object->configureCacheStorage($listener, $storage);
    }


    /**
     * @covers Flower\View\Pane\Service\AbstractCacheListenerFactory::configureSerializer
     */
    public function testConfigureSerializerWithString()
    {
        $listener = $this->getMock('Flower\View\Pane\ManagerListener\CacheListenerInterface');
        $serializer = 'Zend\Serializer\Adapter\PhpSerialize';
        $listener->expects($this->once())
                ->method('setSerializer')
                ->with($this->isInstanceOf($serializer));

        $this->object->configureSerializer($listener, $serializer);
    }

    public function testConfigureSerializerWithObject()
    {
        $listener = $this->getMock('Flower\View\Pane\ManagerListener\CacheListenerInterface');
        $serializer = new PhpSerialize;
        $listener->expects($this->once())
                ->method('setSerializer')
                ->with($this->equalTo($serializer));

        $this->object->configureSerializer($listener, $serializer);
    }

    /**
     * @covers Flower\View\Pane\Service\AbstractCacheListenerFactory::configureExtra
     */
    public function testConfigureExtraWithServiceNameViaServiceLocator()
    {
        $listener = $this->getMock('Flower\Domain\DomainServiceAwareInterface');
        $serviceLocator = new ServiceManager;
        $config = array(
            'domain_service' => 'TestDomainService',
        );
        $domainService = new DomainService;
        $serviceLocator->setService('TestDomainService', $domainService);
        $listener->expects($this->once())
                ->method('setDomainService')
                ->with($this->equalTo($domainService));

        $this->object->configureExtra($serviceLocator, $listener, $config);
    }

    /**
     * @covers Flower\View\Pane\Service\AbstractCacheListenerFactory::configureExtra
     */
    public function testConfigureExtraWithObject()
    {
        $listener = $this->getMock('Flower\Domain\DomainServiceAwareInterface');
        $serviceLocator = new ServiceManager;
        $domainService = new DomainService;
        $config = array(
            'domain_service' => $domainService,
        );

        $listener->expects($this->once())
                ->method('setDomainService')
                ->with($this->equalTo($domainService));

        $this->object->configureExtra($serviceLocator, $listener, $config);
    }
}