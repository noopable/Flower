<?php
namespace FlowerTest\Domain;

use Flower\Domain\ServiceFactory;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-01-25 at 00:15:13.
 */
class ServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceFactory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ServiceFactory;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\Domain\ServiceFactory::createService
     */
    public function testCreateService()
    {
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $res = $this->object->createService($serviceLocator);
        $this->assertInstanceOf('Flower\Domain\Service', $res);
    }
}
