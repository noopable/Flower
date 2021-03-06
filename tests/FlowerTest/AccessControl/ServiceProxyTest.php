<?php
namespace FlowerTest\AccessControl;

use Flower\AccessControl\ServiceProxy;
use Zend\Permissions\Acl\Role\GenericRole;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-01-12 at 17:59:41.
 */
class ServiceProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceProxy
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->acl = $this->getMock('Zend\Permissions\Acl\Acl');
        $this->role = new GenericRole('guest');
        $this->resource = $this->getMock('Zend\Permissions\Acl\Resource\ResourceInterface', array('getResourceId', 'doServiceMethod'));
        $methodMap = array(
            'doServiceMethod' => 'invoke',
        );
        $this->object = new ServiceProxy($this->acl, $this->resource, $this->role, $methodMap);
        $this->ref = new \ReflectionObject($this->object);
        $this->prop = $this->ref->getProperty('methodPrivilegeMap');
        $this->prop->setAccessible(true);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\AccessControl\ServiceProxy::isAllowed
     */
    public function testIsAllowed()
    {
        $this->acl->expects($this->once())
                ->method('isAllowed')
                ->with($this->equalTo($this->role), $this->equalTo($this->resource), $this->equalTo('invoke'))
                ->will($this->returnValue(TRUE));
        $res = $this->object->isAllowed('invoke');
        $this->assertTrue($res);
    }
    
    public function testIsAllowedNotAllowed()
    {
        $this->acl->expects($this->once())
                ->method('isAllowed')
                ->with($this->equalTo($this->role), $this->equalTo($this->resource), $this->equalTo('invoke'))
                ->will($this->returnValue(false));
        $res = $this->object->isAllowed('invoke');
        $this->assertFalse($res);
    }
    
    public function testIsAllowedWithMethodMapFlag()
    {
        $this->acl->expects($this->once())
                ->method('isAllowed')
                ->with($this->equalTo($this->role), $this->equalTo($this->resource), $this->equalTo('invoke'))
                ->will($this->returnValue(TRUE));
        $res = $this->object->isAllowed('doServiceMethod', true);
        $this->assertTrue($res);
    }

    /**
     * @covers Flower\AccessControl\ServiceProxy::passThrough
     */
    public function testPassThrough()
    {
        $this->assertSame($this->resource, $this->object->passThrough());
    }

    /**
     * @covers Flower\AccessControl\ServiceProxy::__call
     */
    public function test__call()
    {
        $this->acl->expects($this->once())
                ->method('isAllowed')
                ->with($this->equalTo($this->role), $this->equalTo($this->resource), $this->equalTo('invoke'))
                ->will($this->returnValue(TRUE));
        $this->resource->expects($this->once())
                ->method('doServiceMethod')
                ->with($this->equalTo('foo'))
                ->will($this->returnValue('bar'));
        $res = $this->object->doServiceMethod('foo');
        $this->assertEquals('bar', $res);
    }
    
    /**
     * @covers Flower\AccessControl\ServiceProxy::__call
     * @expectedException Flower\AccessControl\Exception\RuntimeException
     */
    public function testNotAllowedCall()
    {
        $this->acl->expects($this->once())
                ->method('isAllowed')
                ->with($this->equalTo($this->role), $this->equalTo($this->resource), $this->equalTo('invoke'))
                ->will($this->returnValue(false));
        $this->resource->expects($this->never())
                ->method('doServiceMethod');
        $this->object->doServiceMethod('foo');
    }
}
