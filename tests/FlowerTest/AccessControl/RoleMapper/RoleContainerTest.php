<?php
namespace FlowerTest\AccessControl\RoleMapper;

use Flower\AccessControl\RoleMapper\RoleContainer;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-01-14 at 14:31:29.
 */
class RoleContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RoleContainer
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new RoleContainer('dummy');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\AccessControl\RoleMapper\RoleContainer::setParents
     * @todo   Implement testSetParents().
     */
    public function testSetParents()
    {
        $parents = array('foo', 'bar');
        $ref = new \ReflectionObject($this->object);
        $prop = $ref->getProperty('parents');
        $prop->setAccessible(true);
        $this->object->setParents($parents);
        $this->assertEquals($parents, $prop->getValue($this->object));
    }
    
    /**
     * @depends testSetParents
     * @todo   Implement testGetParents().
     */
    public function testGetParents()
    {
        $parents = array('foo', 'bar');
        $this->object->setParents($parents);
        $this->assertEquals($parents, $this->object->getParents());
    }
    
    /**
     * @depends testGetParents
     * @todo   Implement testAddParent().
     */
    public function testAddParent()
    {
        $parents = array('foo', 'bar');
        $this->object->setParents($parents);
        $this->object->addParent('baz');
        $this->assertEquals(array('foo', 'bar', 'baz'), $this->object->getParents());
    }


}
