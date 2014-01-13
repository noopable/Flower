<?php
namespace FlowerTest\Resource;

use Flower\Resource\ResourceClass\Resource;
use Flower\Resource\ResourcePluginManager;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-01-04 at 11:24:50.
 */
class ResourcePluginManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ResourcePluginManager
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ResourcePluginManager;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testGetWithInvokable()
    {
        $resource = $this->object->get('standard');
        $this->assertInstanceOf('Flower\Resource\ResourceClass\ResourceInterface', $resource);
    }
    
    public function testShareByDefaultBehavior()
    {
        $this->object->get('standard');
        $services = $this->object->getRegisteredServices();
        $this->assertCount(0, $services['instances']);
        
        $ref = new \ReflectionObject($this->object);
        $prop = $ref->getProperty('shareByDefault');
        $prop->setAccessible(true);
        $prop->setValue($this->object, true);
        
        $this->object->get('standard');
        $services = $this->object->getRegisteredServices();
        $this->assertCount(1, $services['instances']);
        $this->assertEquals('standard', $services['instances'][0]);
    }
    /**
     * @covers Flower\Resource\ResourcePluginManager::validatePlugin
     */
    public function testValidatePlugin()
    {
        $notResource = new \stdClass;
        $this->assertFalse($this->object->validatePlugin($notResource));
        
        $resource = new Resource;
        $this->assertTrue($this->object->validatePlugin($resource));
        
        $this->assertFalse($this->object->validatePlugin(null));
        
    }
}