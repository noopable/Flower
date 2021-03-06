<?php
namespace FlowerTest\EventManager\Event;

use Flower\EventManager\Event\EventAbstractFactory;
use Flower\Test\TestTool;
use Zend\ServiceManager\ServiceManager;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-24 at 22:56:10.
 */
class EventAbstractFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventAbstractFactory
     */
    protected $object;

    protected $serviceLocator;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new EventAbstractFactory;
        $this->serviceLocator = new ServiceManager;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testAddClass()
    {
        $class = 'stdClass';
        $this->object->addClass($class);
        $classes = TestTool::getPropertyValue($this->object, 'classes');
        $this->assertEquals($classes['stdclass'], 'stdClass');
    }

    /**
     * @covers Flower\EventManager\Event\EventAbstractFactory::canCreateServiceWithName
     */
    public function testCanCreateServiceWithName()
    {
        $this->assertTrue($this->object->canCreateServiceWithName($this->serviceLocator, 'zendeventmanagerevent', 'Zend\EventManager\Event'));
        $this->assertFalse($this->object->canCreateServiceWithName($this->serviceLocator, 'stdclass', 'stdClass'));
        $class = 'stdClass';
        $this->object->addClass($class);
        $this->assertTrue($this->object->canCreateServiceWithName($this->serviceLocator, 'stdclass', 'stdClass'));
    }

    /**
     * @covers Flower\EventManager\Event\EventAbstractFactory::createServiceWithName
     */
    public function testCreateServiceWithName()
    {
        $options = array(
            'name' => $name = 'foo',
            'target' => $target = 'bar',
            'params' => $params = array('a' => 'b'),
        );
        $this->object->setCreationOptions($options);
        $event = $this->object->createServiceWithName($this->serviceLocator, 'zendeventmanagerevent', 'Zend\EventManager\Event');
        $this->assertInstanceOf('Zend\EventManager\Event', $event);
        $this->assertEquals($name, $event->getName());
        $this->assertEquals($target, $event->getTarget());
        $this->assertEquals($params, $event->getParams());
    }

    /**
     * @covers Flower\EventManager\Event\EventAbstractFactory::setCreationOptions
     */
    public function testSetCreationOptions()
    {
        $options = array('name' => 'foo');
        $this->object->setCreationOptions($options);
        $this->assertEquals($options, TestTool::getPropertyValue($this->object, 'creationOptions'));
    }
}
