<?php
namespace FlowerTest\View\Pane;

use Flower\View\Pane\Builder;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-12-20 at 20:33:18.
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Builder
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Builder;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\View\Pane\Builder::setPaneClass
     * @todo   Implement testSetPaneClass().
     */
    public function testSetPaneClass()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Flower\View\Pane\Builder::build
     * @todo   Implement testBuild().
     */
    public function testBuildDefaultPane()
    {
        $paneConfig = array('tag' => '','inner' => array('classes' => 'container'));
        $pane = $this->object->build($paneConfig);
        
        $this->assertFalse($pane->hasChildren());
        $children = $pane->current();
        $this->assertInstanceOf('Flower\View\Pane\PaneInterface', $children);
    }

    /**
     * @covers Flower\View\Pane\Builder::getNewPane
     * @todo   Implement testGetNewPane().
     */
    public function testGetNewPane()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
    
    public function testSizeToClass()
    {
        
        $reflection = new \ReflectionClass('Flower\View\Pane\Builder');
        $method = $reflection->getMethod('sizeToClass');
        $method->setAccessible(true);
        $property = $reflection->getProperty('sizeToClassFunction');
        $property->setAccessible(true);
        
        //default action  tw bootstrap 2
        $string1 = $method->invokeArgs($this->object, array(1));
        $this->assertEquals('span1', $string1);
        
        //custom function
        $property->setValue($this->object, function ($size) {return (string) ($size * 2);} );
        $string2 = $method->invokeArgs($this->object, array(2));
        $this->assertEquals('4', $string2);
    }
            
}
