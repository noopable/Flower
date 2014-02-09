<?php
namespace FlowerTest\View\Pane\ManagerListener;

use Flower\Test\TestTool;
use Zend\ServiceManager\ServiceManager;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-09 at 22:18:52.
 */
class AbstractLazyLoadCacheListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractLazyLoadCacheListener
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new TestAsset\ConcreteLazyLoadCacheListener;
        $this->serviceLocator = new ServiceManager;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\View\Pane\ManagerListener\AbstractLazyLoadCacheListener::setCacheServiceName
     */
    public function testSetCacheServiceName()
    {
        $name = 'foo';
        $this->object->setCacheServiceName($name);
        $this->assertEquals($name, TestTool::getPropertyValue($this->object, 'cacheServiceName'));
    }

    /**
     * @depends testSetCacheServiceName
     * @covers Flower\View\Pane\ManagerListener\AbstractLazyLoadCacheListener::getCacheServiceName
     */
    public function testGetCacheServiceName()
    {
        $name = 'foo';
        $this->object->setCacheServiceName($name);
        $this->assertEquals($name, $this->object->getCacheServiceName());
    }

    /**
     * @covers Flower\View\Pane\ManagerListener\AbstractLazyLoadCacheListener::setStorage
     */
    public function testSetStorageNull()
    {
        $name = 'foo';
        $cacheService = $this->getMock('Zend\Cache\Storage\StorageInterface');
        $this->serviceLocator->setService($name, $cacheService);
        $this->object->setServiceLocator($this->serviceLocator);
        $this->object->setCacheServiceName($name);
        $this->assertSame($cacheService, $this->object->getStorage());
    }

}