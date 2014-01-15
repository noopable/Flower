<?php
namespace FlowerTest\AccessControl;

use Flower\AccessControl\AccessControlServiceFactory;
use Zend\ServiceManager\ServiceManager;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-01-14 at 21:54:20.
 */
class AccessControlServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AccessControlServiceFactory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new AccessControlServiceFactory;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\AccessControl\AccessControlServiceFactory::createService
     */
    public function testCreateService()
    {
        $serviceLocator = new ServiceManager;
        
        $service = $this->object->createService($serviceLocator);
        $this->assertInstanceOf('Flower\AccessControl\AccessControlService', $service);
    }
    
    /**
     * ServiceConfigが正常である前提
     * 
     */
    public function testCreateServiceWithConfig()
    {
        $aclPath = __DIR__ . '/TestAsset/acl.scirpt.php';
        $config = array(
            'flower_access_control' => array(
                'acl_path' => $aclPath,
            ),
        );
        
        $serviceLocator = new ServiceManager;
        $serviceLocator->setService('Config', $config);
        
        $service = $this->object->createService($serviceLocator);
        $this->assertInstanceOf('Flower\AccessControl\AccessControlService', $service);
        $this->assertEquals($aclPath, $service->getAclScriptPath());
    }
    
    public function testCreateServiceWithMockDi()
    {
        $di = $this->getMock('Zend\Di\Di');
        $serviceLocator = new ServiceManager;
        $serviceLocator->setService('Di', $di);
        $di->expects($this->once())
                ->method('get')
                ->with($this->equalTo('Flower\AccessControl\AccessControlService'))
                ->will($this->returnValue(new \Flower\AccessControl\AccessControlService));
        $service = $this->object->createService($serviceLocator);
        $this->assertInstanceOf('Flower\AccessControl\AccessControlService', $service);
    }
    
    public function testCreateServiceWithMockDiNoUse()
    {
        $config = array(
            'flower_access_control' => array(
                'use_di' => false,
            ),
        );
        $di = $this->getMock('Zend\Di\Di');
        
        $serviceLocator = new ServiceManager;
        $serviceLocator->setService('config', $config);
        $serviceLocator->setService('Di', $di);
        $di->expects($this->never())
                ->method('get');
        $service = $this->object->createService($serviceLocator);
        $this->assertInstanceOf('Flower\AccessControl\AccessControlService', $service);
    }
    
    public function testCreateServiceWithConcreteDi()
    {
        $di = new \Zend\Di\Di;
        $serviceLocator = new ServiceManager;
        $serviceLocator->setService('Di', $di);
        $service = $this->object->createService($serviceLocator);
        $this->assertInstanceOf('Flower\AccessControl\AccessControlService', $service);
    }
}
