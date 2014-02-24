<?php
namespace FlowerTest\EventManager\Event;

use Flower\EventManager\Event\EventPluginManager;
use Zend\ServiceManager\Config;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-24 at 10:03:01.
 */
class EventPluginManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventPluginManager
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new EventPluginManager;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\EventManager\Event\EventPluginManager::validatePlugin
     */
    public function testValidatePlugin()
    {
        $plugin = $this->getMock('Zend\EventManager\EventInterface');
        $this->object->validatePlugin($plugin);
    }


    /**
     * @expectedException Flower\EventManager\Exception\RuntimeException
     */
    public function testValidatePluginInvalidObject()
    {
        $plugin = new \stdClass;
        $this->object->validatePlugin($plugin);
    }

    public function testConfiguredPluginManager()
    {
        $config = array(
            'invokables' => array('Zend\EventManager\Event' => 'Zend\EventManager\Event'),
            'aliases' => array('foo' => 'Zend\EventManager\Event'),
        );
        $serviceConfig = new Config($config);
        $serviceConfig->configureServiceManager($this->object);
        $this->assertTrue($this->object->hasAlias('foo'));
        $res = $this->object->get('foo');
        $this->assertInstanceOf('Zend\EventManager\Event', $res);
    }
}
