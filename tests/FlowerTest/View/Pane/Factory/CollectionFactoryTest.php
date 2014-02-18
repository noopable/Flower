<?php
namespace FlowerTest\View\Pane\Factory;

use Flower\Test\TestTool;
use Flower\View\Pane\Builder\Builder;
use Flower\View\Pane\Factory\CollectionFactory;
use Flower\View\Pane\PaneClass\EntityScriptPane;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-14 at 13:37:27.
 */
class CollectionFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers Flower\View\Pane\Factory\CollectionFactory::factory
     */
    public function testFactoryWithCollection()
    {
        $builder = new Builder;
        $data = array(
            new \stdClass,
            array('foo' => 'bar'),
        );
        $collection = new \RecursiveArrayIterator;
        $prototype = new EntityScriptPane;
        $paneConfig = array(
            'tag' => $tag = 'foo',
            'order' => $order = 5,
            'size' => $size = 10,
            'var' => $var = 'header',
            'classes' => $classes = 'container row',
            'collection' => $collection,
            'prototype' => $prototype,
            'attributes' => $attributes = array(
                'foo' => 'bar',
                'baz' => 'qux',
            ),
        );

        $pane = CollectionFactory::factory($paneConfig, $builder);
        $this->assertInstanceOf('Flower\View\Pane\PaneClass\Collection', $pane);
        $this->assertEquals('<!-- start container pane -->', $pane->containerBegin());
        $this->assertEquals('<!-- end container pane -->', $pane->containerEnd());
        $this->assertEquals($var, TestTool::getPropertyValue($pane, '_var'));
        $this->assertEquals($prototype, $pane->getPrototype());
        $this->assertEquals($collection->current(), $pane->current()->getEntity());

    }
}
