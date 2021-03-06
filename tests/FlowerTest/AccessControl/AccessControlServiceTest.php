<?php
namespace FlowerTest\AccessControl;

use Flower\AccessControl\AccessControlService;
use Flower\AccessControl\RoleMapper\RoleMapperInterface;
use Flower\AccessControl\RoleMapper\RoleContainer;
use Flower\Test\TestTool;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Role\GenericRole;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-01-14 at 21:54:14.
 */
class AccessControlServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AccessControlService
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new AccessControlService;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::authenticate
     */
    public function testAuthenticate()
    {
        $result = $this->getMockBuilder('Zend\Authentication\Result')
                ->disableOriginalConstructor()
                ->getMock();
        $result->expects($this->once())
                ->method('getIdentity')
                ->will($this->returnValue('foo'));
        $result->expects($this->once())
                ->method('isValid')
                ->will($this->returnValue(true));
        $acl = $this->getMock('Zend\Permissions\Acl\Acl');

        $authService = $this->getMock('Zend\Authentication\AuthenticationService');
        $authService->expects($this->once())
                ->method('authenticate')
                ->will($this->returnValue($result));

        $this->object->setAcl($acl);
        $this->object->setAuthService($authService);
        $this->object->authenticate();
        return array('object' => $this->object, 'result' => $result);
    }

        /**
     * @covers Flower\AccessControl\AccessControlService::authenticate
     */
    public function testAuthenticateWithParams()
    {
        $identity = 'foo';
        $credential = 'bar';
        $result = $this->getMockBuilder('Zend\Authentication\Result')
                ->disableOriginalConstructor()
                ->getMock();
        $result->expects($this->once())
                ->method('getIdentity')
                ->will($this->returnValue($identity));
        $result->expects($this->once())
                ->method('isValid')
                ->will($this->returnValue(true));
        $acl = $this->getMock('Zend\Permissions\Acl\Acl');

        $adapter = $this->getMock('Zend\Authentication\Adapter\AbstractAdapter');
        $adapter->expects($this->once())
                ->method('setIdentity')
                ->with($this->equalTo($identity));
        $adapter->expects($this->once())
                ->method('setCredential')
                ->with($this->equalTo($credential));
        $authService = $this->getMock('Zend\Authentication\AuthenticationService');
        $authService->expects($this->once())
                ->method('getAdapter')
                ->will($this->returnValue($adapter));
        $authService->expects($this->once())
                ->method('authenticate')
                ->will($this->returnValue($result));

        $this->object->setAcl($acl);
        $this->object->setAuthService($authService);
        $res = $this->object->authenticate($identity, $credential);
        $this->assertTrue($res);
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::isLoggedIn
     */
    public function testIsLoggedInSuccessAndCache()
    {
        $identity = 'foo';
        $authService = $this->getMock('Zend\Authentication\AuthenticationService');
        $authService->expects($this->once())
                ->method('hasIdentity')
                ->will($this->returnValue(true));
        $authService->expects($this->once())
                ->method('getIdentity')
                ->will($this->returnValue($identity));
        $acl = $this->getMock('Zend\Permissions\Acl\Acl');
        $this->object->setAcl($acl);
        $this->object->setAuthService($authService);
        $this->assertTrue($this->object->isLoggedIn());
        $this->assertTrue($this->object->isLoggedIn());
    }
    /**
     * @covers Flower\AccessControl\AccessControlService::isLoggedIn
     */
    public function testIsLoggedInFailAndCache()
    {
        $authService = $this->getMock('Zend\Authentication\AuthenticationService');
        $authService->expects($this->once())
                ->method('hasIdentity')
                ->will($this->returnValue(false));
        $acl = $this->getMock('Zend\Permissions\Acl\Acl');
        $this->object->setAcl($acl);
        $this->object->setAuthService($authService);
        $this->assertFalse($this->object->isLoggedIn());
        $this->assertFalse($this->object->isLoggedIn());
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::getIdentity
     */
    public function testGetIdentity()
    {
        $authService = $this->getMock('Zend\Authentication\AuthenticationService');
        $authService->expects($this->once())
                ->method('hasIdentity')
                ->will($this->returnValue(false));
        $this->object->setAuthService($authService);
        $this->assertNull($this->object->getIdentity());
    }

    public function getIdentitySuccess()
    {
        $identity = 'foo';
        $authService = $this->getMock('Zend\Authentication\AuthenticationService');
        $authService->expects($this->once())
                ->method('hasIdentity')
                ->will($this->returnValue(true));
        $authService->expects($this->once())
                ->method('getIdentity')
                ->will($this->returnValue($identity));
        $this->assertEquals($identity, $this->object->getIdentity());
    }

    /**
     * @depends testAuthenticate
     * @covers Flower\AccessControl\AccessControlService::getAuthResult
     */
    public function testGetAuthResult($authDepend)
    {
        $this->assertSame($authDepend['result'], $authDepend['object']->getAuthResult());
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::setRole
     */
    public function testSetRole()
    {
        $role = $this->getMock('Zend\Permissions\Acl\Role\RoleInterface');
        $this->object->setRole($role);
        $this->assertSame($role, TestTool::getPropertyValue($this->object, 'role'));
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::getRole
     */
    public function testGetRole()
    {
        $role = $this->getMock('Zend\Permissions\Acl\Role\RoleInterface');
        $this->object->setRole($role);
        $this->assertSame($role, $this->object->getRole());
    }

    /**
     * @expectedException Flower\AccessControl\Exception\RuntimeException
     */
    public function testGetRoleWithoutPreset()
    {
        $this->object->getRole();
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::setAcl
     */
    public function testSetAcl()
    {
        $acl = $this->getMock('Zend\Permissions\Acl\Acl');
        $this->object->setAcl($acl);
        $this->assertSame($acl, TestTool::getPropertyValue($this->object, 'acl'));
    }

    public function testHasBuiltInRoles()
    {
        $builtInRoles = TestTool::getPropertyValue($this->object, 'builtInRoles');
        $this->assertCount(3, $builtInRoles);
        $this->assertContains(RoleMapperInterface::BUILT_IN_AUTHENTICATED_CLIENT, $builtInRoles);
        $this->assertContains(RoleMapperInterface::BUILT_IN_CURRENT_CLIENT_AGGREGATE, $builtInRoles);
        $this->assertContains(RoleMapperInterface::BUILT_IN_NOT_AUTHENTICATED_CLIENT, $builtInRoles);
    }

    /**
     * @depends testHasBuiltInRoles
     * @covers Flower\AccessControl\AccessControlService::injectBuiltInRoles
     */
    public function testInjectBuiltInRoles()
    {
        $roles = array(
            RoleMapperInterface::BUILT_IN_AUTHENTICATED_CLIENT,
            //BuiltInRolesではあるが、動的にroleをバインドするため認証後に個別にセットされる
            //キャッシュもされない。
            //RoleMapperInterface::BUILT_IN_CURRENT_CLIENT_AGGREGATE,
            RoleMapperInterface::BUILT_IN_NOT_AUTHENTICATED_CLIENT,
        );
        $acl = $this->getMock('Zend\Permissions\Acl\Acl');
        $acl->expects($this->exactly(2))
                ->method('hasRole')
                ->with($this->callback(function ($param) use ($roles) { return in_array($param, $roles);}))
                ->will($this->returnValue(false));
        $acl->expects($this->exactly(2))
                ->method('addRole')
                ->with($this->callback(function ($param) use ($roles) { return in_array($param, $roles);}));
        $this->object->injectBuiltInRoles($acl);
    }

    /**
     *
     * @depends testInjectBuiltInRoles
     */
    public function testSetAclInjectBuiltInRoles()
    {
        $roles = array(
            RoleMapperInterface::BUILT_IN_AUTHENTICATED_CLIENT,
            //BuiltInRolesではあるが、動的にroleをバインドするため認証後に個別にセットされる
            //キャッシュもされない。
            //RoleMapperInterface::BUILT_IN_CURRENT_CLIENT_AGGREGATE,
            RoleMapperInterface::BUILT_IN_NOT_AUTHENTICATED_CLIENT,
        );
        $acl = $this->getMock('Zend\Permissions\Acl\Acl');
        $acl->expects($this->exactly(2))
                ->method('hasRole')
                ->with($this->callback(function ($param) use ($roles) { return in_array($param, $roles);}))
                ->will($this->returnValue(false));
        $acl->expects($this->exactly(2))
                ->method('addRole')
                ->with($this->callback(function ($param) use ($roles) { return in_array($param, $roles);}));
        $this->object->setAcl($acl);
    }

    /**
     * @expectedException Flower\AccessControl\Exception\RuntimeException
     * @covers Flower\AccessControl\AccessControlService::getAcl
     */
    public function testGetAclWithoutPreset()
    {
        $this->object->getAcl();
    }

    /**
     * @depends testSetAcl
     * @covers Flower\AccessControl\AccessControlService::getAcl
     */
    public function testGetAcl()
    {
        $acl = $this->getMock('Zend\Permissions\Acl\Acl');
        $this->object->setAcl($acl);
        $this->assertSame($acl, $this->object->getAcl());
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::injectRoleToAcl
     */
    public function testInjectRoleToAcl()
    {
        $role = new RoleContainer('edit');
        $parents = array('guest');
        $role->setParents($parents);
        $acl = $this->getMock('Zend\Permissions\Acl\Acl');
        $acl->expects($this->once())
                ->method('addRole')
                ->with($role, $parents);

        $this->object->injectRoleToAcl($role, $acl);
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::injectRoleToAcl
     */
    public function testInjectGenericRoleToAcl()
    {
        $role = new GenericRole('edit');
        $acl = $this->getMock('Zend\Permissions\Acl\Acl');
        $acl->expects($this->once())
                ->method('addRole')
                ->with($role);

        $this->object->injectRoleToAcl($role, $acl);
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::wrap
     * @todo   Implement testWrap().
     */
    public function testWrap()
    {
        $underControls = array('stdClass');
        $response = $this->getMockBuilder('Flower\AccessControl\ServiceProxy')
                ->disableOriginalConstructor()->getMock();
        $service = new \stdClass;
        $wrapper = $this->getMock('Flower\AccessControl\AccessControlWrapper');
        $wrapper->expects($this->once())
                ->method('wrap')
                ->with($this->equalTo($service))
                ->will($this->returnValue($response));
        $this->object->setUnderAccessControls($underControls);
        $this->object->setAccessControlWrapper($wrapper);
        $res = $this->object->wrap('stdClass', $service);
        $this->assertSame($response, $res);
    }

    public function testWrapNotUnderControl()
    {
        $service = new \stdClass;
        $wrapper = $this->getMock('Flower\AccessControl\AccessControlWrapper');
        $wrapper->expects($this->never())->method('wrap');
        $this->object->setAccessControlWrapper($wrapper);
        $res = $this->object->wrap('foo', $service);
        $this->assertSame($service, $res);
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::isAllowed
     */
    public function testIsAllowed()
    {
        $acl = new Acl;
        $role = new GenericRole('guest');
        $resource = new GenericResource('news');
        $acl->addRole($role);
        $acl->addResource($resource);
        $this->object->setAcl($acl);
        $this->object->setRole($role);
        /**
         * ロールを暗黙的に取得
         */
        $this->assertFalse($acl->isAllowed($role, $resource, null));
        $this->assertFalse($this->object->isAllowed($resource, null));

        $acl->allow($role, $resource, 'publish');
        $this->assertTrue($acl->isAllowed($role, $resource, 'publish'));
        $this->assertTrue($this->object->isAllowed($resource, 'publish'));
    }

    public function testIsAllowedInvalidRole()
    {
        $acl = new Acl;
        $role = new GenericRole('guest');
        //$acl->addRole($role);
        $this->object->setAcl($acl);
        /**
         *
         * AccessControlServiceではチェック対象のロールを自動的にAclに追加するので、
         * Aclに明示的に追加されていないロールでも例外にならない。
         * ZF2の標準のAclではロールがない場合に、InvalidArgumentExceptionをraiseする。
         */
        $this->object->setRole($role);
        /**
         * ロールを暗黙的に取得
         */
        $this->assertFalse($acl->isAllowed($role, null, null));
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::setAuthService
     */
    public function testSetAuthService()
    {
        $authService = $this->getMock('Zend\Authentication\AuthenticationService');
        $this->object->setAuthService($authService);
        $this->assertSame($authService, TestTool::getPropertyValue($this->object, 'authService'));
    }

    /**
     * @depends testSetAuthService
     * @covers Flower\AccessControl\AccessControlService::getAuthService
     */
    public function testGetAuthService()
    {
        $authService = $this->getMock('Zend\Authentication\AuthenticationService');
        $this->object->setAuthService($authService);
        $this->assertSame($authService, $this->object->getAuthService());
    }

    /**
     * @depends testSetAuthService
     * @covers Flower\AccessControl\AccessControlService::getRole
     */
    public function testGetRoleWithAuthService()
    {
        $result = $this->getMockBuilder('Zend\Authentication\Result')
                ->disableOriginalConstructor()
                ->getMock();
        $result->expects($this->once())
                ->method('getIdentity')
                ->will($this->returnValue('foo'));
        $result->expects($this->once())
                ->method('isValid')
                ->will($this->returnValue(true));
        $acl = $this->getMock('Zend\Permissions\Acl\Acl');

        $authService = $this->getMock('Zend\Authentication\AuthenticationService');
        $authService->expects($this->once())
                ->method('authenticate')
                ->will($this->returnValue($result));

        $this->object->setAcl($acl);
        $this->object->setAuthService($authService);
        $this->object->getRole(true);
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::getCurrentClientData
     */
    public function testGetCurrentClientData()
    {
        $identity = 'foo';
        $data = new \stdClass;
        $authService = $this->getMock('Zend\Authentication\AuthenticationService');
        $authService->expects($this->once())
                ->method('getIdentity')
                ->will($this->returnValue($identity));
        $resourceStorage = $this->getMock('Flower\AccessControl\AuthClient\ResourceStorageInterface');
        $resourceStorage->expects($this->once())
                ->method('setIdentity')
                ->with($this->equalTo($identity));
        $resourceStorage->expects($this->once())
                ->method('getCurrentClientData')
                ->will($this->returnValue($data));
        $this->object->setResourceStorage($resourceStorage);
        $this->object->setAuthService($authService);
        $res = $this->object->getCurrentClientData();
        $this->assertSame($data, $res);
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::setRoleMapper
     */
    public function testSetRoleMapper()
    {
        $roleMapper = $this->getMock('Flower\AccessControl\RoleMapper\RoleMapperInterface');
        $this->object->setRoleMapper($roleMapper);
        $this->assertSame($roleMapper, TestTool::getPropertyValue($this->object, 'roleMapper'));
    }

    /**
     * @depends testSetRoleMapper
     * @covers Flower\AccessControl\AccessControlService::getRoleMapper
     */
    public function testGetRoleMapper()
    {
        $roleMapper = $this->getMock('Flower\AccessControl\RoleMapper\RoleMapperInterface');
        $this->object->setRoleMapper($roleMapper);
        $this->assertSame($roleMapper, $this->object->getRoleMapper());
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::setResourceStorage
     */
    public function testSetResourceStorage()
    {
        $resourceStorage = $this->getMock('Flower\AccessControl\AuthClient\ResourceStorageInterface');
        $this->object->setResourceStorage($resourceStorage);
        $this->assertSame($resourceStorage, TestTool::getPropertyValue($this->object, 'resourceStorage'));
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::getResourceStorage
     */
    public function testGetResourceStorage()
    {
        $resourceStorage = $this->getMock('Flower\AccessControl\AuthClient\ResourceStorageInterface');
        $this->object->setResourceStorage($resourceStorage);
        $this->assertSame($resourceStorage, $this->object->getResourceStorage());
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::setResourceManager
     */
    public function testSetResourceManager()
    {
        $manager = $this->getMock('Flower\Resource\Manager\ManagerInterface');
        $this->object->setResourceManager($manager);
        $this->assertSame($manager, TestTool::getPropertyValue($this->object, 'resourceManager'));
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::getResourceManager
     */
    public function testGetResourceManager()
    {
        $manager = $this->getMock('Flower\Resource\Manager\ManagerInterface');
        $this->object->setResourceManager($manager);
        $this->assertSame($manager, $this->object->getResourceManager());
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::setAccessControlWrapper
     * @todo   Implement testSetAccessControlWrapper().
     */
    public function testSetAccessControlWrapper()
    {
        $accessControlWrapper = $this->getMockBuilder('Flower\AccessControl\AccessControlWrapper')
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->object->setAccessControlWrapper($accessControlWrapper);
        $this->assertSame($accessControlWrapper, TestTool::getPropertyValue($this->object, 'accessControlWrapper'));
    }

    /**
     * @depends testSetAccessControlWrapper
     * @covers Flower\AccessControl\AccessControlService::getAccessControlWrapper
     */
    public function testGetAccessControlWrapper()
    {
        $accessControlWrapper = $this->getMockBuilder('Flower\AccessControl\AccessControlWrapper')
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->object->setAccessControlWrapper($accessControlWrapper);
        $this->assertSame($accessControlWrapper, $this->object->getAccessControlWrapper());
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::setMethodPrivilegeMaps
     * @todo   Implement testSetMethodPrivilegeMaps().
     */
    public function testSetMethodPrivilegeMaps()
    {
        $maps = array(
            'Foo/Bar' => array(
                'methodA' => 'publish',
                'methodB' => 'edit',
            ),
            'Foo/Baz' => array(
                'methodC' => 'publish',
                'methodD' => 'create'
            ),
        );
        $this->object->setMethodPrivilegeMaps($maps);
        $this->assertEquals($maps, TestTool::getPropertyValue($this->object, 'methodPrivilegeMaps'));
    }

    /**
     * @depends testSetMethodPrivilegeMaps
     * @covers Flower\AccessControl\AccessControlService::getMethodPrivilegeMap
     */
    public function testGetMethodPrivilegeMap()
    {
        $maps = array(
            'Foo/Bar' => array(
                'methodA' => 'publish',
                'methodB' => 'edit',
            ),
            'Foo/Baz' => array(
                'methodC' => 'publish',
                'methodD' => 'create'
            ),
        );
        $this->object->setMethodPrivilegeMaps($maps);
        $this->assertEquals($maps['Foo/Bar'], $this->object->getMethodPrivilegeMap('Foo/Bar'));
    }

    /**
     * @depends testGetMethodPrivilegeMap
     * @covers Flower\AccessControl\AccessControlService::addMethodPrivilegeMap
     */
    public function testAddMethodPrivilegeMap()
    {
        $maps = array(
            'Foo/Bar' => array(
                'methodA' => 'publish',
                'methodB' => 'edit',
            ),
            'Foo/Baz' => array(
                'methodC' => 'publish',
                'methodD' => 'create'
            ),
        );
        $this->object->setMethodPrivilegeMaps($maps);
        $add = array(
                'methodE' => 'publish',
                'methodF' => 'delete'
            );
        $this->object->addMethodPrivilegeMap('Foo/Qux', $add);
        $this->assertEquals($add, $this->object->getMethodPrivilegeMap('Foo/Qux'));
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::setUnderAccessControls
     */
    public function testSetUnderAccessControls()
    {
        $underAccessControls = array('Publish', 'CRUD');
        $this->object->setUnderAccessControls($underAccessControls);
        $this->assertEquals(array('publish' => 'publish', 'crud' => 'crud'), TestTool::getPropertyValue($this->object, 'underAccessControls'));
    }

    /**
     * @depends testSetUnderAccessControls
     * @covers Flower\AccessControl\AccessControlService::getUnderAccessControls
     */
    public function testGetUnderAccessControls()
    {
        $underAccessControls = array('Publish', 'CRUD');
        $this->object->setUnderAccessControls($underAccessControls);
        $this->assertEquals(array('publish' => 'publish', 'crud' => 'crud'), $this->object->getUnderAccessControls());
    }

    /**
     * @depends testSetUnderAccessControls
     * @covers Flower\AccessControl\AccessControlService::isUnderAccessControl
     */
    public function testIsUnderAccessControl()
    {
        $underAccessControls = array('Publish', 'CRUD');
        $this->object->setUnderAccessControls($underAccessControls);
        $this->assertTrue($this->object->isUnderAccessControl('Publish'));
        $this->assertFalse($this->object->isUnderAccessControl('Foo\Bar'));
    }

    /**
     * @depends testGetUnderAccessControls
     * @covers Flower\AccessControl\AccessControlService::addUnderAccessControl
     */
    public function testAddUnderAccessControl()
    {
        $underAccessControls = array('Publish', 'CRUD');
        $this->object->setUnderAccessControls($underAccessControls);
        $this->object->addUnderAccessControl('edit');
        $this->assertEquals(array('publish' => 'publish', 'crud' => 'crud', 'edit' => 'edit'), $this->object->getUnderAccessControls());
    }

    /**
     * @depends testIsUnderAccessControl
     * @covers Flower\AccessControl\AccessControlService::removeUnderAccessControl
     */
    public function testRemoveUnderAccessControl()
    {
        $underAccessControls = array('Publish', 'CRUD');
        $this->object->setUnderAccessControls($underAccessControls);
        $this->object->removeUnderAccessControl('Publish');
        $this->assertFalse($this->object->isUnderAccessControl('Publish'));
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::setAclLoader
     */
    public function testSetAclLoader()
    {
        $aclLoader = $this->getMockBuilder('Flower\AccessControl\AclLoader')
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->object->setAclLoader($aclLoader);
        $this->assertSame($aclLoader, TestTool::getPropertyValue($this->object, 'aclLoader'));
    }

    /**
     * @depends testSetAclLoader
     * @covers Flower\AccessControl\AccessControlService::getAclLoader
     */
    public function testGetAclLoader()
    {
        $aclLoader = $this->getMockBuilder('Flower\AccessControl\AclLoader')
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->object->setAclLoader($aclLoader);
        $this->assertSame($aclLoader, $this->object->getAclLoader());
    }

    public function testGetAclLoaderWithAclScriptPath()
    {
        $this->object->setAclScriptPath(__DIR__);
        $aclLoader = $this->object->getAclLoader();
        $this->assertInstanceOf('Flower\AccessControl\AclLoader', $aclLoader);
    }

    /**
     * @depends testGetAclLoader
     * @covers Flower\AccessControl\AccessControlService::getAcl
     */
    public function testGetAclWithAclLoader()
    {
        $acl = $this->getMock('Zend\Permissions\Acl\Acl');
        $aclLoader = $this->getMockBuilder('Flower\AccessControl\AclLoader')
                ->disableOriginalConstructor()
                ->getMock();
        $aclLoader->expects($this->once())
                ->method('load')
                ->will($this->returnValue($acl));
        $this->object->setAclLoader($aclLoader);
        $this->assertSame($acl, $this->object->getAcl());
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::setAclScriptPath
     * @covers Flower\AccessControl\ACSSetterGetterTrait::setAclScriptPath
     */
    public function testSetAclScriptPath()
    {
        $aclScript = '/tmp/dummy';
        $this->object->setAclScriptPath($aclScript);
        $this->assertEquals($aclScript, TestTool::getPropertyValue($this->object, 'aclScriptPath'));
    }

    /**
     * @depends testSetAclScriptPath
     * @covers Flower\AccessControl\AccessControlService::getAclScriptPath
     * @covers Flower\AccessControl\ACSSetterGetterTrait::getAclScriptPath
     */
    public function testGetAclScriptPath()
    {
        $aclScript = '/tmp/dummy';
        $this->object->setAclScriptPath($aclScript);
        $this->assertEquals($aclScript, $this->object->getAclScriptPath());
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::setAuthResultReturnColumns
     * @covers Flower\AccessControl\ACSSetterGetterTrait::setAuthResultReturnColumns
     */
    public function testSetAuthResultReturnColumns()
    {
        $columns = array('real_name', 'roles');
        $this->object->setAuthResultReturnColumns($columns);
        $this->assertEquals($columns, TestTool::getPropertyValue($this->object,'returnColumns'));
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::setAuthResultOmitColumns
     * @covers Flower\AccessControl\ACSSetterGetterTrait::setAuthResultOmitColumns
     */
    public function testSetAuthResultOmitColumns()
    {
        $columns = array('password');
        $this->object->setAuthResultOmitColumns($columns);
        $this->assertEquals($columns, TestTool::getPropertyValue($this->object,'omitColumns'));
    }

    /**
     * @covers Flower\AccessControl\AccessControlService::getAuthResultRowObject
     */
    public function testGetAuthResultRowObject()
    {
        $returnColumns = array('username', 'role');
        $omitColumns = array('password');
        $object = new \stdClass;
        $authService = $this->getMock('Zend\Authentication\AuthenticationService');
        $adapter = $this->getMockBuilder('Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter')
                ->disableOriginalConstructor()
                ->getMock();
        $authService->expects($this->once())
                ->method('getAdapter')
                ->will($this->returnValue($adapter));
        $adapter->expects($this->once())
                ->method('getResultRowObject')
                ->with($this->equalTo($returnColumns), $this->equalTo($omitColumns))
                ->will($this->returnValue($object));
        $this->object->setAuthResultReturnColumns($returnColumns);
        $this->object->setAuthResultOmitColumns($omitColumns);
        $this->object->setAuthService($authService);
        $this->object->getAuthResultRowObject($returnColumns, $omitColumns);
    }

}
