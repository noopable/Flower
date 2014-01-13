<?php
namespace FlowerTest\Resource\ResourceClass;

use Flower\Resource\ResourceClass\Resource;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-01-03 at 23:24:23.
 */
class ResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Resource
     */
    protected $object;

    /**
     *
     * @var \ReflectionObject;
     */
    protected $ref;
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Resource;
        $this->ref = new \ReflectionObject($this->object);
        
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\Resource\ResourceClass\Resource::setOptions
     */
    public function testSetOptions()
    {
        $options = array('foo' => 'bar');
        $this->object->setOptions($options);
        $prop = $this->ref->getProperty('options');
        $prop->setAccessible(true);
        $this->assertEquals($options, $prop->getValue($this->object));
        
    }

    /**
     * @covers Flower\Resource\ResourceClass\Resource::getOptions
     */
    public function testGetOptions()
    {
        $options = array('foo' => 'bar');
        $this->object->setOptions($options);
        $this->assertEquals($options, $this->object->getOptions());
    }

    /**
     * @covers Flower\Resource\ResourceClass\Resource::getData
     */
    public function testGetData()
    {
        $data = new \stdClass;
        $this->object->setData($data);
        $this->assertEquals($data, $this->object->getData());
    }

    /**
     * @covers Flower\Resource\ResourceClass\Resource::getInnerId
     */
    public function testGetInnerId()
    {
        $innerId = 356;
        $this->object->setInnerId($innerId);
        $this->assertEquals($innerId, $this->object->getInnerId());
    }

    /**
     * @covers Flower\Resource\ResourceClass\Resource::getProperties
     */
    public function testGetProperties()
    {
        $properties = array('foo' => 'bar');
        $this->object->setProperties($properties);
        $this->assertEquals($properties, $this->object->getProperties());
    }

    /**
     * @covers Flower\Resource\ResourceClass\Resource::setResourceId
     */
    public function testSetResourceId()
    {
        $arg = 'resource  id';
        $this->object->setResourceId($arg);
        $prop = $this->ref->getProperty('resourceId');
        $prop->setAccessible(true);
        $this->assertEquals($arg, $prop->getValue($this->object));
    }

    /**
     * @covers Flower\Resource\ResourceClass\Resource::getResourceId
     */
    public function testGetResourceId()
    {
        $arg = 'resource  id';
        $this->object->setResourceId($arg);
        $this->assertEquals($arg, $this->object->getResourceId());
    }
    
    public function testAutoGeneratedResourceId()
    {
        $type = 'foo';
        $innerId = 235;
        //$prop = $this->ref->getProperty('delimiter');
        //$prop->setAccessible(true);
        //$delimiter = $prop->getValue($this->object);
        $delimiter = Resource::getDelimiter();
        $this->object->setType($type);
        $this->object->setInnerId($innerId);
        $this->assertEquals('foo' . $delimiter . '235', $this->object->getResourceId());
    }

    /**
     * @covers Flower\Resource\ResourceClass\Resource::getType
     */
    public function testGetType()
    {
        $type = 'bar';
        $this->object->setType($type);
        $this->assertEquals($type, $this->object->getType());
    }

    /**
     * @covers Flower\Resource\ResourceClass\Resource::setData
     */
    public function testSetData()
    {
        $data = 'resource  id';
        $this->object->setData($data);
        $prop = $this->ref->getProperty('data');
        $prop->setAccessible(true);
        $this->assertEquals($data, $prop->getValue($this->object));
    }

    /**
     * @covers Flower\Resource\ResourceClass\Resource::setProperties
     */
    public function testSetProperties()
    {
        $data = array('standard');
        $this->object->setProperties($data);
        $prop = $this->ref->getProperty('properties');
        $prop->setAccessible(true);
        $this->assertEquals($data, $prop->getValue($this->object));
    }

    /**
     * @covers Flower\Resource\ResourceClass\Resource::setType
     */
    public function testSetType()
    {
        $data = 'standard';
        $this->object->setType($data);
        $prop = $this->ref->getProperty('type');
        $prop->setAccessible(true);
        $this->assertEquals($data, $prop->getValue($this->object));
    }

    /**
     * @covers Flower\Resource\ResourceClass\Resource::toString
     */
    public function testToString()
    {
        //by default data to string by json encode
        $this->object->setData(array('foo' => 'bar'));
        $this->assertEquals('{"foo":"bar"}', $this->object->toString());
    }

    public function testToStringWithSerializeOptionPhp()
    {
        //by default data to string by json encode
        $this->object->setData(array('foo' => 'bar'));
        $options = $this->object->getOptions();
        $options['serialize_policy'] = 'php';
        $this->object->setOptions($options);
        $this->assertEquals('a:1:{s:3:"foo";s:3:"bar";}', $this->object->toString());
    }
    
    public function testToStringWithSerializeOptionCast()
    {
        //by default data to string by json encode
        $this->object->setData(345);
        $options = $this->object->getOptions();
        $options['serialize_policy'] = 'cast';
        $this->object->setOptions($options);
        $this->assertInternalType('string', $this->object->toString());
        $this->assertEquals('345', $this->object->toString());
    }
    /**
     * @covers Flower\Resource\ResourceClass\Resource::__toString
     */
    public function test__toString()
    {
        //by default data to string by json encode
        $this->object->setData(array('foo' => 'bar'));
        $this->assertEquals($this->object->toString(), $this->object->__toString());
    }

    /**
     * @covers Flower\Resource\ResourceClass\Resource::setInnerId
     */
    public function testSetInnerId()
    {
        $data = 356;
        $this->object->setInnerId($data);
        $prop = $this->ref->getProperty('innerId');
        $prop->setAccessible(true);
        $this->assertEquals($data, $prop->getValue($this->object));
    }
    
    /**
     * @covers Flower\Resource\ResourceClass\Resource::getDelimiter
     */
    public function testGetDelimiter()
    {
        $prop = $this->ref->getProperty('delimiter');
        $prop->setAccessible(true);
        $this->assertEquals($prop->getValue(), Resource::getDelimiter());
    }
}