<?php
namespace FlowerTest\ServiceLayer\Events;

use Flower\ServiceLayer\Events\EventsProxyFactory;
use FlowerTest\ServiceLayer\TestAsset\ServiceForTest;
use Zend\EventManager\EventManager;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-01-16 at 13:19:55.
 */
class EventsProxyFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventsProxyFactory
     */
    protected $object;
    
    protected $eventManager;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->eventManager = new EventManager;
        $this->object = new EventsProxyFactory($this->eventManager);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\ServiceLayer\Events\EventsProxyFactory::factory
     */
    public function testFactory()
    {
        $service = new ServiceForTest;
        $wrapped = $this->object->factory($service);
        $this->assertInstanceOf('Flower\ServiceLayer\Events\EventsProxy', $wrapped);
        $this->assertSame($this->eventManager, $wrapped->getEventManager());
        
    }
}
