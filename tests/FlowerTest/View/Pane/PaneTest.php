<?php

namespace FlowerTest\View\Pane;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Flower\View\Pane\Pane;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-04-05 at 15:22:58.
 */
class PaneTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Pane
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Pane;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * @covers Flower\View\Pane\Pane::build
     */
    public function testBuild()
    {
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
        $paneConfig = array('tag' => '','inner' => array('classes' => 'container'));
        $this->object->build($paneConfig);
        
        $this->assertFalse($this->object->hasChildren());
        $children = $this->object->current();
        $this->assertInstanceOf('Flower\View\Pane\Pane', $children);
    }

    /**
     * @covers Flower\View\Pane\Pane::getOrder
     * @todo   Implement testGetOrder().
     */
    public function testGetOrder()
    {
        $this->assertEquals(1, $this->object->getOrder());
    }

    /**
     * @covers Flower\View\Pane\Pane::insert
     * @todo   Implement testInsert().
     */
    public function testInsert()
    {
        $pane = new \Flower\View\Pane\Pane;
        $this->assertFalse($pane->hasChildren());
        // Remove the following lines when you implement this test.
        //$this->assertTrue($this->object->current() instanceof \Flower\RecursivePriorityQueue);
        $this->assertFalse($this->object->hasChildren());
        $this->object->insert(new \Flower\View\Pane\Pane, 100);
        //RecursiveIterator must return a current entry has child
        $this->object->current()->insert(new \Flower\View\Pane\Pane);
        $this->assertTrue($this->object->hasChildren());
        
    }

}
