<?php
namespace Flower\EventManager\Service;

use Zend\ServiceManager\ServiceManager;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-24 at 10:03:18.
 */
class RegistryFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegistryFactory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new RegistryFactory;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testCreateServiceWithMinimumConfiguration()
    {
        $config = array(
            'flower_events_registry' => array(
                
            ),
        );
        $serviceLocator = new ServiceManager;
        $serviceLocator->setService('Config', $config);
        $res = $this->object->createService($serviceLocator);
        $this->assertInstanceOf('Flower\File\Gateway\GatewayInterface', $res);
    }
}
