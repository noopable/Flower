<?php
/*
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace FlowerTest;

use Flower\Test\TestTool;
use FlowerTest\TestAsset\DispatcherTrait;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Model\ViewModel;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-11-16 at 04:51:19.
 */
class DispatcherTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DispatcherTrait
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new DispatcherTrait;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\DispatcherTrait::setControllerName
     */
    public function testSetControllerName()
    {
        $this->object->setControllerName('foo');
        $this->assertEquals('foo', TestTool::getPropertyValue($this->object, 'controllerName'));
    }

    /**
     * @covers Flower\DispatcherTrait::setDispatchOptions
     */
    public function testSetDispatchOptions()
    {
        $options = array(
            'foo' => 'bar',
        );
        $this->object->setDispatchOptions($options);
        $this->assertEquals($options, TestTool::getPropertyValue($this->object, 'dispatchOptions'));
    }

    /**
     * シグネチャはいらないね。
     *
     * @covers Flower\DispatcherTrait::setSignature
     */
    public function testSetSignature()
    {
        $signature = array(
            'foo' => 'bar',
        );
        $this->object->setSignature($signature);
        $this->assertEquals($signature, TestTool::getPropertyValue($this->object, 'signature'));
    }

    /**
     * @covers Flower\DispatcherTrait::dispatch
     */
    public function testDispatch()
    {
        $controllerName = 'foo';
        $dispatchOptions = array('a' => 'b');
        $resultModel = new ViewModel;

        $plugin = $this->getMockBuilder('Zend\\Mvc\\Controller\\Plugin\\Forward')
                ->disableOriginalConstructor()->getMock();
        $plugin->expects($this->once())
                ->method('dispatch')
                ->with($this->equalTo($controllerName), $this->equalTo($dispatchOptions))
                ->will($this->returnValue($resultModel));

        $pluginManager = $this->getMock('Zend\\Mvc\\Controller\\PluginManager');
        $pluginManager->expects($this->once())
                ->method('get')
                ->with($this->equalTo('forward'))
                ->will($this->returnValue($plugin));
        $sl = new ServiceManager;
        $sl->setService('ControllerPluginManager', $pluginManager);

        $this->object->setServiceLocator($sl);
        $this->object->setControllerName($controllerName);
        $this->object->setDispatchOptions($dispatchOptions);
        $this->assertSame($resultModel, $this->object->dispatch());
    }
}
