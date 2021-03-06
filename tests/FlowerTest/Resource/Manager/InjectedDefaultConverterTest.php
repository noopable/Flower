<?php
namespace FlowerTest\Resource\Manager;

use FlowerTest\Bootstrap;
use Flower\Resource\Converter\DefaultConverter;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-12-31 at 23:41:20.
 */
class DefaultConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultConverter
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $manager = $this->serviceManager->get('Flower_Resource_Manager');
        $this->object = $manager->getConverter();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\Resource\Converter\DefaultConverter::getResourcePluginManager
     */
    public function testGetResourcePluginManager()
    {
        $resourcePluginManager = $this->object->getResourcePluginManager();
        $this->assertInstanceOf('Flower\Resource\ResourcePluginManager', $resourcePluginManager);
        return $resourcePluginManager;
    }
    
    /**
     * @depends testGetResourcePluginManager
     */
    public function testProvidedResourcePluginManagerHasConfigured($resourcePluginManager)
    {
        $resource = $resourcePluginManager->get('generic');
        $this->assertInstanceOf('Flower\Resource\ResourceClass\ResourceInterface', $resource);
    }
}
