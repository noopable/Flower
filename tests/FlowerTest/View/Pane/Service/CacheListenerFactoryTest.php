<?php
namespace FlowerTest\View\Pane\Service;

use Flower\Test\TestTool;
use Flower\View\Pane\Service\CacheListenerFactory;
use Zend\ServiceManager\ServiceManager;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-07 at 20:28:32.
 */
class CacheListenerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CacheListenerFactory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new CacheListenerFactory;
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
     * @covers Flower\View\Pane\Service\CacheListenerFactory::createService
     */
    public function testCreateServiceWithoutConfig()
    {
        $res = $this->object->createService($this->serviceLocator);
        $this->assertNull($res);
    }

    /**
     * @covers Flower\View\Pane\Service\ConfigFileListenerFactory::createService
     */
    public function testCreateService()
    {
        $config = require 'TestAsset/cache_listener.config.php';
        $this->serviceLocator->setService('Config', $config);
        $service = $this->object->createService($this->serviceLocator);
        $this->assertInstanceOf('Flower\View\Pane\Service\CacheListener', $service);
    }
}