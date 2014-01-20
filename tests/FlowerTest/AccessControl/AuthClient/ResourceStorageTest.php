<?php
namespace FlowerTest\AccessControl\AuthClient;

use Flower\AccessControl\AuthClient\ResourceStorage;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-01-14 at 19:55:05.
 */
class ResourceStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ResourceStorage
     */
    protected $object;
    
    /**
     *
     * @var Flower\AccessControl\AccessControlService
     */
    protected $service;
    
    /**
     *
     * @var \ReflectionObject
     */
    protected $ref;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->service = $this->getMock('Flower\AccessControl\AccessControlService');
        $this->object = new ResourceStorage($this->service);
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
     * @covers Flower\AccessControl\AuthClient\ResourceStorage::setAccessControlService
     */
    public function testSetAccessControlService()
    {
        $service = $this->getMock('Flower\AccessControl\AccessControlService');
        $prop = $this->ref->getProperty('service');
        $prop->setAccessible(true);
        $this->object->setAccessControlService($service);
        $this->assertSame($service, $prop->getValue($this->object));
    }

    /**
     * @depends testSetAccessControlService
     * @covers Flower\AccessControl\AuthClient\ResourceStorage::getAccessControlService
     */
    public function testGetAccessControlService()
    {
        $service = $this->getMock('Flower\AccessControl\AccessControlService');
        $this->object->setAccessControlService($service);
        $this->assertSame($service, $this->object->getAccessControlService());
    }

    /**
     * @covers Flower\AccessControl\AuthClient\ResourceStorage::setIdentity
     */
    public function testSetIdentity()
    {
        $identity = 'foo';
        $prop = $this->ref->getProperty('identity');
        $prop->setAccessible(true);
        $this->object->setIdentity($identity);
        $this->assertEquals($identity, $prop->getValue($this->object));
    }

    /**
     * @depends testSetIdentity
     * @covers Flower\AccessControl\AuthClient\ResourceStorage::getIdentity
     */
    public function testGetIdentity()
    {
        $identity = 'foo';
        $this->object->setIdentity($identity);
        $this->assertEquals($identity, $this->object->getIdentity());
    }
    /**
     * @covers Flower\AccessControl\AuthClient\ResourceStorage::getBareBoneClientResource
     */
    public function testGetBareBoneClientResource()
    {
        $identity = 'foo';
        $resource = $this->object->getBareBoneClientResource($identity);
        $this->assertInstanceOf('Flower\AccessControl\AuthClient\AuthClientResource', $resource);
        $this->assertEquals($identity, $resource->getIdentity());
    }

    /**
     * @depends testSetIdentity
     * @covers Flower\AccessControl\AuthClient\ResourceStorage::getResourceId
     */
    public function testGetResourceId()
    {
        $this->assertNull($this->object->getResourceId());
        $identity = 'foo';
        $this->object->setIdentity($identity);
        $this->assertEquals('authClient_7FFFFFFF', $this->object->getResourceId());
    }

    /**
     * @covers Flower\AccessControl\AuthClient\ResourceStorage::getCurrentClientResource
     */
    public function testGetCurrentClientResource()
    {
        $this->assertNull($this->object->getCurrentClientResource());
        
        $manager = $this->getMock('Flower\Resource\Manager\ManagerInterface');
        $resource = $this->getMock('Flower\AccessControl\AuthClient\AuthClientResource');
        
        $this->assertInstanceOf('Flower\Resource\ResourceClass\ResourceInterface', $resource);
        
        $identity = 'foo';
        $this->object->setIdentity($identity);
        $resourceId = $this->object->getResourceId();
        
        $manager->expects($this->once())
                ->method('get')
                ->with($this->equalTo($resourceId))
                ->will($this->returnValue($resource));
        
        $this->object->setResourceManager($manager);
        $res = $this->object->getCurrentClientResource();
        $this->assertSame($resource, $res);
    }

    /**
     * @covers Flower\AccessControl\AuthClient\ResourceStorage::getCurrentClientData
     */
    public function testGetCurrentClientData()
    {
        $manager = $this->getMock('Flower\Resource\Manager\ManagerInterface');
        $resource = $this->getMock('Flower\AccessControl\AuthClient\AuthClientResource');
        
        $this->assertInstanceOf('Flower\Resource\ResourceClass\ResourceInterface', $resource);
        
        $identity = 'foo';
        $this->object->setIdentity($identity);
        $resourceId = $this->object->getResourceId();
        $data = new \stdClass;
        $data->a = 'b';
        
        $resource->expects($this->once())
                ->method('getData')
                ->will($this->returnValue($data));
        $manager->expects($this->once())
                ->method('get')
                ->with($this->equalTo($resourceId))
                ->will($this->returnValue($resource));
        
        $this->object->setResourceManager($manager);
        $this->assertSame($data, $this->object->getCurrentClientData());
    }

    /**
     * @covers Flower\AccessControl\AuthClient\ResourceStorage::setResourceManager
     */
    public function testSetResourceManager()
    {
        $manager = $this->getMock('Flower\Resource\Manager\ManagerInterface');
        $this->object->setResourceManager($manager);
        $prop = $this->ref->getProperty('resourceManager');
        $prop->setAccessible(true);
        $this->assertSame($manager, $prop->getValue($this->object));
    }

    /**
     * @depends testSetResourceManager
     * @covers Flower\AccessControl\AuthClient\ResourceStorage::getResourceManager
     */
    public function testGetResourceManager()
    {
        $manager = $this->getMock('Flower\Resource\Manager\ManagerInterface');
        $this->object->setResourceManager($manager);
        $this->assertSame($manager, $this->object->getResourceManager());
    }

    /**
     * @covers Flower\AccessControl\AuthClient\ResourceStorage::isEmpty
     */
    public function testIsEmpty()
    {
        $this->assertTrue($this->object->isEmpty());
        $this->object->setIdentity('foo');
        $this->assertFalse($this->object->isEmpty());
    }

    /**
     * @depends testGetResourceId
     * @covers Flower\AccessControl\AuthClient\ResourceStorage::read
     */
    public function testRead()
    {
        $this->assertNull($this->object->read());
        
        $identity = 'foo';
        $this->object->setIdentity($identity);
        $this->assertEquals($identity, $this->object->read());
    }

    /**
     * StorageInterface::write will be called with the valid identity
     *  when AuthenticationService::authenticate is success
     * In this phase DbAdapter already has ResultRowObject.
     *  Thus, We can get resultObject in the method write.  
     * 
     * @covers Flower\AccessControl\AuthClient\ResourceStorage::write
     */
    public function testWrite()
    {
        $object = new \stdClass;
        $object->role = 'admin';
        $object->password = 'lisjeli';
        //returnColumnsが指定された場合は、omitColumnsは考慮されない
        $manager = $this->getMock('Flower\Resource\Manager\ManagerInterface');
        $this->object->setResourceManager($manager);
        $service = $this->getMock('Flower\AccessControl\AccessControlService');
        $this->object->setAccessControlService($service);
        $service->expects($this->once())
                ->method('getAuthResultRowObject')
                ->will($this->returnValue($object));
        $manager->expects($this->once())
                ->method('saveResource')
                ->with($this->isInstanceOf('Flower\Resource\ResourceClass\ResourceInterface'));
        $this->object->write('foo');
        $this->assertEquals('foo', $this->object->getIdentity());
    }

    /**
     * @covers Flower\AccessControl\AuthClient\ResourceStorage::clear
     */
    public function testClear()
    {
        $this->assertNull($this->object->clear());
        //ok no action
    }
}
