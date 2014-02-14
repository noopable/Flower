<?php
namespace FlowerTest\View\Pane\PaneClass;

use Flower\View\Pane\Builder\Builder;
use Flower\View\Pane\Factory\EntityScriptPaneFactory;
use Flower\View\Pane\PaneClass\EntityScriptPane;
use Flower\View\Pane\PaneRenderer;
use Zend\View\Renderer\PhpRenderer;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-14 at 11:43:53.
 */
class EntityScriptPaneTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityScriptPane
     */
    protected $object;

    protected $builder;

    /**
     *
     * @var array
     */
    protected $entity;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->builder = new Builder(array('pane_class' => 'Flower\View\Pane\PaneClass\EntityScriptPane'));
        $this->entity = array('foo' => 'bar');
        $this->object = new EntityScriptPane;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\View\Pane\PaneClass\EntityScriptPane::setEntity
     */
    public function testSetGetEntity()
    {
        $this->object->setEntity($this->entity);
        $this->assertEquals($this->entity, $this->object->getEntity());
    }

    /**
     * @covers Flower\View\Pane\PaneClass\EntityScriptPane::_render
     * @todo   Implement test_render().
     */
    public function test_render()
    {
        $expected =
'<!-- begin Renderer -->
<div>
<div>
  <div id="foo">
  <!-- start content entity -->
array(1) {
  ["foo"]=>
  string(3) "bar"
}
  <!-- end content entity -->
  </div>
</div>
</div>
<!-- end Renderer -->
';
        $expected = str_replace("\r\n","\n", $expected);
        $paneConfig = array(
            'id' => 'foo',
            'var' => 'entity', //コンテンツをキャンセル
        );
        $pane = EntityScriptPaneFactory::factory($paneConfig, $this->builder);
        $pane->setEntity($this->entity);
        $view = new PhpRenderer;
        $view->resolver()->addPath(__DIR__ . '/TestAsset/view');
        $renderer = new PaneRenderer($pane);
        $renderer->setView($view);
        //コンテンツなし
        $this->assertEquals($expected, str_replace("\r\n","\r", (string) $renderer));
    }
}