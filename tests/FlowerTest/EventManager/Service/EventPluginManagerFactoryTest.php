<?php
namespace FlowerTest\EventManager\Service;


use Flower\EventManager\Service\EventPluginManagerFactory;
use Flower\Test\TestTool;
use FlowerTest\Bootstrap;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-24 at 10:03:07.
 */
class EventPluginManagerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventPluginManagerFactory
     */
    protected $object;

    protected $serviceLocator;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new EventPluginManagerFactory;
        $this->serviceLocator = Bootstrap::getServiceManager();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\EventManager\Service\EventPluginManagerFactory::createService
     */
    public function testCreateService()
    {
        $res = $this->object->createService($this->serviceLocator);
        $this->assertInstanceOf('Flower\EventManager\Event\EventPluginManager', $res);
        $this->assertInstanceOf('Zend\ServiceManager\Di\DiAbstractServiceFactory', TestTool::getPropertyValue($res, 'abstractFactories')[0]);
    }
}
