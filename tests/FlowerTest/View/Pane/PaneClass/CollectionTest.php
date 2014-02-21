<?php
namespace FlowerTest\View\Pane\PaneClass;

use Flower\Test\TestTool;
use Flower\View\Pane\PaneClass\Collection;
use RecursiveArrayIterator;


/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-14 at 10:32:57.
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Collection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->data = array(
            array('id' => 'foo'),
            array('id' => 'bar'),
            array('id' => 'baz'),
            new \stdClass,
        );
        $this->collection = new RecursiveArrayIterator($this->data);
        $this->prototype = new TestAsset\ConcreteEntityAwarePane;
        $this->object = new Collection;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\View\Pane\PaneClass\Collection::setPrototype
     */
    public function testSetPrototype()
    {
        $this->object->setPrototype($this->prototype);
        $this->assertSame($this->prototype, TestTool::getPropertyValue($this->object, 'prototype'));
    }

    /**
     * @covers Flower\View\Pane\PaneClass\Collection::getPrototype
     */
    public function testGetPrototype()
    {
        $this->object->setPrototype($this->prototype);
        $this->assertEquals($this->prototype, $this->object->getPrototype(), 'getPrototype clone prototype');
    }

    /**
     * @covers Flower\View\Pane\PaneClass\Collection::setCollection
     */
    public function testSetCollection()
    {
        $this->object->setCollection($this->collection);
        $this->assertSame($this->collection, TestTool::getPropertyValue($this->object, 'collection'));
    }

    /**
     * @covers Flower\View\Pane\PaneClass\Collection::getCollection
     */
    public function testGetCollection()
    {
        $this->object->setCollection($this->collection);
        $this->assertSame($this->collection, $this->object->getCollection());
    }

    /**
     * @covers Flower\View\Pane\PaneClass\Collection::current
     */
    public function testCurrent()
    {
        $this->object->setCollection($this->collection);
        $this->object->setPrototype($this->prototype);
        $current = $this->object->current();
        $this->assertInstanceOf(get_class($this->prototype), $current);
        $this->assertSame($this->collection->current(), $current->getEntity());
    }

    /**
     * @covers Flower\View\Pane\PaneClass\Collection::getChildren
     */
    public function testGetChildren()
    {
        $this->assertNull($this->object->getChildren());
    }

    /**
     * @covers Flower\View\Pane\PaneClass\Collection::hasChildren
     */
    public function testHasChildren()
    {
        $this->assertFalse($this->object->hasChildren());
    }

    /**
     * @expectedException Flower\View\Pane\Exception\RuntimeException
     * @covers Flower\View\Pane\PaneClass\Collection::insert
     */
    public function testInsert()
    {
        $this->object->insert(new \stdClass, 1);
    }

    /**
     * @covers Flower\View\Pane\PaneClass\Collection::key
     */
    public function testKey()
    {
        $this->object->setCollection($this->collection);
        $this->assertEquals($this->collection->key(), $this->object->key());
        $this->object->next();
        $this->assertEquals($this->collection->key(), $this->object->key());
        $this->object->next();
        $this->assertEquals($this->collection->key(), $this->object->key());
        $this->object->next();
        $this->assertEquals($this->collection->key(), $this->object->key());
    }

    /**
     * @covers Flower\View\Pane\PaneClass\Collection::rewind
     */
    public function testRewind()
    {
        $this->object->setCollection($this->collection);
        $this->object->setPrototype($this->prototype);
        $this->assertEquals($this->collection->key(), $this->object->key());
        $this->object->next();
        $this->assertEquals($this->collection->key(), $this->object->key());
        $this->object->rewind();
        $this->assertEquals($this->collection->current(), $this->object->current()->getEntity());
    }

    /**
     * @covers Flower\View\Pane\PaneClass\Collection::valid
     */
    public function testValid()
    {
        $this->object->setCollection($this->collection);
        $count = count($this->data);
        $this->assertTrue($this->object->valid());
        for ($i = 0; $i < $count; $i++ ) {
            $this->object->next();
        }
        $this->assertFalse($this->object->valid());
    }

    public function testGetIteratorWithIterator()
    {
        $iterator = $this->getMock('Iterator');
        $this->object->setCollection($iterator);
        $this->assertSame($iterator, $this->object->getIterator());
    }

    public function testGetIteratorWithIteratorAggregate()
    {
        $iterator = $this->getMock('Iterator');
        $iteratorAggregate = $this->getMock('IteratorAggregate');
        $iteratorAggregate->expects($this->once())
                ->method('getIterator')
                ->will($this->returnValue($iterator));
        $this->object->setCollection($iteratorAggregate);
        $this->assertSame($iterator, $this->object->getIterator());
    }
}
