<?php
namespace FlowerTest\View\Pane\PaneClass;

use Flower\Test\TestTool;
use Flower\View\Pane\PaneClass\EntityAnchor;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-19 at 10:10:32.
 */
class EntityAnchorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityAnchor
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new EntityAnchor;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\View\Pane\PaneClass\EntityAnchor::setEntity
     */
    public function testSetEntity()
    {
        $entity = new \stdClass;
        $entity->href = 'foo';
        $this->object->setOption('mutable_params', array('href'));
        $this->object->setEntity($entity);
        $this->assertSame($entity, TestTool::getPropertyValue($this->object, 'entity'));
        $this->assertEquals($entity->href, $this->object->href);
    }

    public function testSetEntityApplicatePaneInterface()
    {
        $entity = $this->getMock('Flower\View\Pane\Entity\ApplicatePaneInterface');
        $entity->expects($this->once())
                ->method('apply')
                ->with($this->identicalTo($this->object));
        $this->object->setEntity($entity);
    }

    /**
     * @covers Flower\View\Pane\PaneClass\EntityAnchor::getEntity
     */
    public function testGetEntity()
    {
        $entity = new \stdClass;
        $this->object->setEntity($entity);
        $this->assertSame($entity, $this->object->getEntity());
    }
}
