<?php
namespace FlowerTest\ServiceLayer;

use Flower\Test\TestTool;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-01-17 at 22:19:18.
 */
class AbstractRepoServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractRepoService
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new TestAsset\ConcreteRepoService;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\ServiceLayer\AbstractRepoService::setRepositoryPluginManager
     */
    public function testSetRepositoryPluginManager()
    {
        $repositoryPluginManager = $this->getMock('Flower\Model\Service\RepositoryPluginManager');
        $this->object->setRepositoryPluginManager($repositoryPluginManager);
        $this->assertSame($repositoryPluginManager, TestTool::getPropertyValue($this->object, 'repositoryPluginManager'));
    }

    /**
     * @depends testSetRepositoryPluginManager
     * @covers Flower\ServiceLayer\AbstractRepoService::getRepositoryPluginManager
     */
    public function testGetRepositoryPluginManager()
    {
        $repositoryPluginManager = $this->getMock('Flower\Model\Service\RepositoryPluginManager');
        $this->object->setRepositoryPluginManager($repositoryPluginManager);
        $this->assertSame($repositoryPluginManager, $this->object->getRepositoryPluginManager());
    }
    
    /**
     * @covers Flower\ServiceLayer\AbstractRepoService::setRepositoryName
     */
    public function testSetRepositoryName()
    {
        $repositoryName = 'foo';
        $this->object->setRepositoryName($repositoryName);
        $this->assertEquals($repositoryName, TestTool::getPropertyValue($this->object, 'repositoryName'));
    }

    /**
     * @depends testSetRepositoryName
     * @covers Flower\ServiceLayer\AbstractRepoService::getRepositoryName
     */
    public function testGetRepositoryName()
    {
        $repositoryName = 'foo';
        $this->object->setRepositoryName($repositoryName);
        $this->assertEquals($repositoryName, $this->object->getRepositoryName());
    }

    /**
     * @covers Flower\ServiceLayer\AbstractRepoService::setRepository
     */
    public function testSetRepository()
    {
        $repository = $this->getMock('Flower\Model\RepositoryInterface');
        $this->object->setRepository($repository);
        $this->assertSame($repository, TestTool::getPropertyValue($this->object, 'repository'));
    }

    /**
     * @covers Flower\ServiceLayer\AbstractRepoService::getRepository
     */
    public function testGetRepository()
    {
        $repository = $this->getMock('Flower\Model\RepositoryInterface');
        $this->object->setRepository($repository);
        $this->assertSame($repository, $this->object->getRepository());
    }
    
    public function testGetRepositoryNotSet()
    {
        $this->assertEquals(null, $this->object->getRepository());
    }
    
    public function testGetRepositoryWithSpecifiedName()
    {
        $repository = $this->getMock('Flower\Model\RepositoryInterface');
        $repositoryName = 'foo';
        $repositoryPluginManager = $this->getMock('Flower\Model\Service\RepositoryPluginManager');
        $repositoryPluginManager->expects($this->once())
                ->method('has')
                ->will($this->returnValue(true));
        $repositoryPluginManager->expects($this->once())
                ->method('get')
                ->with($this->equalTo($repositoryName))
                ->will($this->returnValue($repository));
        $this->object->setRepositoryPluginManager($repositoryPluginManager);
        $this->object->setRepositoryName($repositoryName);
        $res = $this->object->getRepository();
        $this->assertSame($repository, $res);
    }
}
