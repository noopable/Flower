<?php
namespace FlowerTest\View\Pane;

use Flower\View\Pane\PaneHelper;
use Flower\View\Pane\HelperFactory;
use FlowerTest\Bootstrap;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-12-21 at 14:09:01.
 */
class HelperFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HelperFactory
     */
    protected $object;

    
    /**
     *
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceLocator;
    
    /**
     *
     * @var array
     */
    protected $config;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->serviceLocator = Bootstrap::getServiceManager();
        $this->config = $this->serviceLocator->get('Config');
        
        $this->object = new HelperFactory;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\View\Pane\HelperFactory::createService
     */
    public function testCreateService()
    {
        $helper = $this->object->createService($this->serviceLocator);
        $this->assertInstanceof('Flower\View\Pane\PaneHelper', $helper);
        return $helper;
    }
    
    /**
     * 
     * @depends testCreateService
     * 
     */
    public function testBuildOptionsIsInjected(PaneHelper $helper)
    {
        $this->assertInstanceof('Flower\View\Pane\PaneHelper', $helper);
        $builder = $helper->getBuilder();
        $ref = new \ReflectionObject($builder);
        $prop = $ref->getProperty('sizeToClassFunction');
        $prop->setAccessible(true);
        //@see FlowerTest/config/autoload/pane.config.php
        $configuredClosure = $prop->getValue($builder);
        $this->assertInstanceof('Closure', $configuredClosure);
        $this->assertEquals('foo', $configuredClosure(0));
        $this->assertEquals('bar', $configuredClosure(1));
        $this->assertEquals('baz', $configuredClosure(2));
    }
}
