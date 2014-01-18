<?php
namespace Flower\AccessControl\RoleMapper;

use Flower\AccessControl\RoleMapper\RoleMapper;
use Flower\AccessControl\RoleMapper\RoleMapperInterface;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-01-14 at 14:38:56.
 */
class RoleMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RoleMapper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new RoleMapper;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\AccessControl\RoleMapper\RoleMapper::setResourceStorage
     */
    public function testSetResourceStorage()
    {
        $resourceStorage = $this->getMock('Flower\AccessControl\AuthClient\IdenticalStorageInterface');
        $ref = new \ReflectionObject($this->object);
        $prop = $ref->getProperty('resourceStorage');
        $prop->setAccessible(true);
        $this->object->setResourceStorage($resourceStorage);
        $this->assertSame($resourceStorage, $prop->getValue($this->object));
    }

    /**
     * @depends testSetResourceStorage
     * @covers Flower\AccessControl\RoleMapper\RoleMapper::getResourceStorage
     * 
     */
    public function testGetResourceStorage()
    {
        $resourceStorage = $this->getMock('Flower\AccessControl\AuthClient\IdenticalStorageInterface');
        $this->object->setResourceStorage($resourceStorage);
        $this->assertSame($resourceStorage, $this->object->getResourceStorage());
    }
    
    /**
     * @covers Flower\AccessControl\RoleMapper\RoleMapper::getRole
     */
    public function testGetRole()
    {
        $res = $this->object->getRole();
        $this->assertInstanceOf('Zend\Permissions\Acl\Role\RoleInterface', $res);
        $this->assertEquals(RoleMapperInterface::BUILT_IN_NOT_AUTHENTICATED_CLIENT, $res->getRoleId());
    }

    public function testGetRoleWithIdentity()
    {
        $res = $this->object->getRole('any');
        $this->assertInstanceOf('Zend\Permissions\Acl\Role\RoleInterface', $res);
        $this->assertEquals(RoleMapperInterface::BUILT_IN_AUTHENTICATED_CLIENT, $res->getRoleId());
    }
    
    public function testGetRoleWithResourceStorageHavingRole()
    {
        $identity = 'foo';
        $rowResultObject = new \stdClass;
        $rowResultObject->role = 'admin';
        $resourceStorage = $this->getMock('Flower\AccessControl\AuthClient\IdenticalStorageInterface');
        $this->object->setResourceStorage($resourceStorage);
        $resourceStorage->expects($this->once())
                ->method('setIdentity')
                ->with($this->equalTo($identity));
        $resourceStorage->expects($this->once())
                ->method('getCurrentClientData')
                ->will($this->returnValue($rowResultObject));
        $role = $this->object->getRole($identity);
        $this->assertInstanceOf('Zend\Permissions\Acl\Role\RoleInterface', $role);
        $this->assertInstanceOf('Flower\AccessControl\RoleMapper\RoleContainer', $role);
        $this->assertEquals(RoleMapperInterface::BUILT_IN_CURRENT_CLIENT_AGGREGATE, $role->getRoleId());
        $parents = $role->getParents();
        $this->assertContains('admin', $parents);
    }
    
    public function testGetRoleWithResourceStorageHavingRoles()
    {
        $identity = 'foo';
        $rowResultObject = new \stdClass;
        $rowResultObject->roles = array('editor', 'publisher');
        $resourceStorage = $this->getMock('Flower\AccessControl\AuthClient\IdenticalStorageInterface');
        $this->object->setResourceStorage($resourceStorage);
        $resourceStorage->expects($this->once())
                ->method('setIdentity')
                ->with($this->equalTo($identity));
        $resourceStorage->expects($this->once())
                ->method('getCurrentClientData')
                ->will($this->returnValue($rowResultObject));
        $role = $this->object->getRole($identity);
        $this->assertInstanceOf('Zend\Permissions\Acl\Role\RoleInterface', $role);
        $this->assertInstanceOf('Flower\AccessControl\RoleMapper\RoleContainer', $role);
        $this->assertEquals(RoleMapperInterface::BUILT_IN_CURRENT_CLIENT_AGGREGATE, $role->getRoleId());
        $parents = $role->getParents();
        $this->assertContains('editor', $parents);
        $this->assertContains('publisher', $parents);
    }
}
