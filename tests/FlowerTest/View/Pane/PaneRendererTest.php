<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace FlowerTest\View;

use Flower\Test\TestTool;
use Flower\View\Pane\PaneRenderer;
use Flower\View\Pane\Builder;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-04-05 at 19:28:01.
 */
class PaneRendererTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var PaneRenderer
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }


    public function testRender()
    {
        $paneConfig = include __DIR__ . '/TestAsset/2column.php';
        $builder =new Builder;
        $pane = $builder->build($paneConfig);
        $paneRenderer = new PaneRenderer($pane);
        $expected = <<<EOD
<!-- begin PaneRenderer -->
<body class='container'>
  <!-- start content header -->
  <div class='container subhead header' id='overview'>
    <!-- var header is not found -->
  </div>
  <!-- end content header -->
  <div class='container'>
    <!-- start content content -->
    <div class='article row pull-right'>
      <!-- var content is not found -->
    </div>
    <!-- end content content -->
    <div style='float:right;' class='sidebar' id='sidebar'>
      <!-- start content sidebar -->
      <div class='span3'>
        <!-- var sidebar is not found -->
      </div>
      <!-- end content sidebar -->
    </div>
  </div>
  <div id='footer'>
    <!-- start content footer -->
    <div class='container'>
      <!-- var footer is not found -->
    </div>
    <!-- end content footer -->
  </div>
</body>

<!-- end PaneRenderer -->
EOD;
        $this->assertXmlStringEqualsXmlString($expected, (string) $paneRenderer);
    }

   public function testRenderWithSize()
    {
        $paneConfig = include __DIR__ . '/TestAsset/2column_size.php';
        $builder =new Builder;
        $pane = $builder->build($paneConfig);
        $paneRenderer = new PaneRenderer($pane);
        $expected = <<<EOD
<!-- begin PaneRenderer -->
<!-- start pane -->
<div class="container">
  <!-- start content header -->
  <div class='container jumbotron subhead header' id='overview'>
    <!-- var header is not found -->
  </div>
  <!-- end content header -->
  <div class='container'>
    <div class='article row pull-right'>
      <div class='span9'>
        <!-- start content categoryImage -->
        <div>
          <!-- var categoryImage is not found -->
        </div>
        <!-- end content categoryImage -->
        <div class='row'>
          <!-- start content content -->
          <div class='span9'>
            <!-- var content is not found -->
          </div>
          <!-- end content content -->
        </div>
      </div>
    </div>
    <div class='sidebar row' id='sidebar'>
      <!-- start content sidebar -->
      <div class='span3'>
        <!-- var sidebar is not found -->
      </div>
      <!-- end content sidebar -->
    </div>
  </div>
  <div id='footer'>
    <!-- start content footer -->
    <div class='container'>
      <!-- var footer is not found -->
    </div>
    <!-- end content footer -->
  </div>
</div>
<!-- end pane -->
<!-- end PaneRenderer -->
EOD;
        $this->assertXmlStringEqualsXmlString($expected, (string) $paneRenderer);
    }

    public function testMustNotRenderNoVar()
    {
        $builder =new Builder;
        $expected =
'<!-- begin PaneRenderer -->
<div>
</div>
<!-- end PaneRenderer -->';
        $pane = $builder->build(array('var' => false));
        $paneRenderer = new PaneRenderer($pane);
        $this->assertXmlStringEqualsXmlString($expected, (string) $paneRenderer, 'checking var = false');
        $pane = $builder->build(array('var' => null));
        $paneRenderer = new PaneRenderer($pane);
        $this->assertXmlStringEqualsXmlString($expected, (string) $paneRenderer, 'checking var = null');
        $pane = $builder->build(array('var' => 'Null'));
        $paneRenderer = new PaneRenderer($pane);
        $this->assertXmlStringEqualsXmlString($expected, (string) $paneRenderer, 'checking var = "Null"');
    }

    public function testAttributeNameOnlyRendering()
    {
        $builder =new Builder;
        $expected = str_replace(array("\r\n","\n","\r"), '',
'<!-- begin PaneRenderer -->
<div ng-view>
</div>
<!-- end PaneRenderer -->'
);

        $pane = $builder->build(array('attributes' => ['ng-view' => null]));
        $paneRenderer = new PaneRenderer($pane);
        $actual = str_replace(array("\r\n","\n","\r"), '', (string) $paneRenderer);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Flower\View\PaneRenderer::setVars
     */
    public function testSetVars()
    {
        $vars = array(
            'foo' => 'bar',
            'bar' => 'baz',
        );
        $builder =new Builder;
        $pane = $builder->build(array('var' => false));
        $paneRenderer = new PaneRenderer($pane);
        $paneRenderer->setVars($vars);
        $this->assertEquals($vars, TestTool::getPropertyValue($paneRenderer, 'vars'));
    }

    /**
     * @covers Flower\View\PaneRenderer::getVars
     */
    public function testGetVars()
    {
        $vars = array(
            'foo' => 'bar',
            'bar' => 'baz',
        );
        $builder =new Builder;
        $pane = $builder->build(array('var' => false));
        $paneRenderer = new PaneRenderer($pane);
        $paneRenderer->setVars($vars);
        $this->assertEquals($vars, $paneRenderer->getVars());
    }

    /**
     * @covers Flower\View\PaneRenderer::setVar
     */
    public function testSetVar()
    {
        $vars = array(
            'foo' => 'bar',
            'bar' => 'baz',
        );
        $builder =new Builder;
        $pane = $builder->build(array('var' => false));
        $paneRenderer = new PaneRenderer($pane);
        $paneRenderer->setVar('foo', 'bar');
        $paneRenderer->setVar('bar', 'baz');
        $this->assertEquals($vars, $paneRenderer->getVars()->getArrayCopy());
    }

    /**
     * @covers Flower\View\PaneRenderer::setView
     */
    public function testSetView()
    {
        $view = $this->getMock('Zend\View\View');
        $builder =new Builder;
        $pane = $builder->build(array('var' => false));
        $paneRenderer = new PaneRenderer($pane);
        $paneRenderer->setView($view);
        $this->assertSame($view, TestTool::getPropertyValue($paneRenderer, 'view'));
    }

    /**
     * @covers Flower\View\PaneRenderer::getView
     */
    public function testGetView()
    {
        $view = $this->getMock('Zend\View\View');
        $builder =new Builder;
        $pane = $builder->build(array('var' => false));
        $paneRenderer = new PaneRenderer($pane);
        $paneRenderer->setView($view);
        $this->assertSame($view, $paneRenderer->getView());
    }

    /**
     * @covers Flower\View\PaneRenderer::beginIteration
     */
    public function testBeginIteration()
    {
        $expectedString = "\n<!-- begin PaneRenderer -->\n" . 'foo';
        $builder =new Builder;
        $pane = $builder->build(array('wrapBegin' => 'foo'));
        $paneRenderer = new PaneRenderer($pane);
        $paneRenderer->beginIteration();
        $this->expectOutputString($expectedString);
    }

    /**
     * @covers Flower\View\PaneRenderer::endIteration
     */
    public function testEndIteration()
    {
        $expectedString = 'foo' . "\n<!-- end PaneRenderer -->\n" ;
        $builder =new Builder;
        $pane = $builder->build(array('wrapEnd' => 'foo'));
        $paneRenderer = new PaneRenderer($pane);
        $paneRenderer->endIteration();
        $this->expectOutputString($expectedString);
    }

    /**
     * @covers Flower\View\PaneRenderer::beginChildren
     * @covers Flower\View\PaneRenderer::endChildren
     */
    public function testBeginChildrenEndChildren()
    {
        $builder =new Builder;
        $pane = $builder->build(array('wrapBegin' => 'foo', 'wrapEnd' => 'bar'));
        $paneRenderer = new PaneRenderer($pane);
        $paneRenderer->beginChildren();
        echo " ";
        $paneRenderer->endChildren();
        $this->expectOutputString('foo bar');
    }

    /**
     * @covers Flower\View\PaneRenderer::current
     */
    public function testCurrentVarUndefined()
    {
        $expected = '  <!-- start content content -->
  <div class="container">
    <!-- var content is not found -->
  </div>
  <!-- end content content -->
';
        $expected = str_replace(array("\n", "\r"), '', $expected);
        $builder =new Builder;
        $pane = $builder->build(
                array(
                    'wrapBegin' => 'foo',
                    'wrapEnd' => 'bar',
                    'inner' => array('classes' => 'container'),
                ));
        $paneRenderer = new PaneRenderer($pane);
        $this->assertTrue($paneRenderer->valid());
        ob_start();
        $paneRenderer->current();
        $res = ob_get_clean();
        $res = str_replace(array("\n", "\r"), '', $res);
        $this->assertEquals($expected, $res);
    }

    /**
     * @covers Flower\View\PaneRenderer::current
     */
    public function testCurrentVarIsOmitted()
    {
        $expected = '  <div class="container">    <!-- var is omitted -->  </div>';
        $builder =new Builder;
        $pane = $builder->build(
                array(
                    'wrapBegin' => 'foo',
                    'wrapEnd' => 'bar',
                    'inner' => array(
                        'classes' => 'container',
                        'var' => null,
                    ),
                ));
        $paneRenderer = new PaneRenderer($pane);
        $this->assertTrue($paneRenderer->valid());
        ob_start();
        $paneRenderer->current();
        $res = ob_get_clean();
        $res = str_replace(array("\n", "\r"), '', $res);
        $this->assertEquals($expected, $res);
    }

    /**
     * @covers Flower\View\PaneRenderer::current
     */
    public function testCurrentVarDefined()
    {
        $expected = '  <!-- start content varName -->
  <div class="container">
article
  </div>
  <!-- end content varName -->
';
        $expected = str_replace(array("\n", "\r"), '', $expected);
        $builder =new Builder;
        $pane = $builder->build(
                array(
                    'wrapBegin' => 'foo',
                    'wrapEnd' => 'bar',
                    'inner' => array(
                        'classes' => 'container',
                        'var' => 'varName',
                    ),
                ));
        $paneRenderer = new PaneRenderer($pane);
        $paneRenderer->setVar('varName', 'article');
        $this->assertTrue($paneRenderer->valid());
        ob_start();
        $paneRenderer->current();
        $res = ob_get_clean();
        $res = str_replace(array("\n", "\r"), '', $res);
        $this->assertEquals($expected, $res);
    }

    /**
     * @covers Flower\View\PaneRenderer::current
     */
    public function testCurrentVarClosure()
    {
        $expected = '  <!-- start content Closure -->
  <div class="container">
article
  </div>
  <!-- end content Closure -->
';
        $expected = str_replace(array("\n", "\r"), '', $expected);
        $builder =new Builder;
        $pane = $builder->build(
                array(
                    'wrapBegin' => 'foo',
                    'wrapEnd' => 'bar',
                    'inner' => array(
                        'classes' => 'container',
                        'var' => function($ren) { return 'article';},
                    ),
                ));
        $paneRenderer = new PaneRenderer($pane);
        $paneRenderer->setVar('varName', 'article');
        $this->assertTrue($paneRenderer->valid());
        ob_start();
        $paneRenderer->current();
        $res = ob_get_clean();
        $res = str_replace(array("\n", "\r"), '', $res);
        $this->assertEquals($expected, $res);
    }

    /**
     * @covers Flower\View\PaneRenderer::current
     */
    public function testCurrentVarIsCallable()
    {
        $object = $this->getMock('stdClass', array('render'));
        $object->expects($this->once())
                ->method('render')
                ->will($this->returnValue('article'));
        $expected = '  <!-- start content Callable -->
  <div class="container">
article
  </div>
  <!-- end content Callable -->
';
        $expected = str_replace(array("\n", "\r"), '', $expected);
        $builder =new Builder;
        $pane = $builder->build(
                array(
                    'wrapBegin' => 'foo',
                    'wrapEnd' => 'bar',
                    'inner' => array(
                        'classes' => 'container',
                        'var' => array($object, 'render'),
                    ),
                ));
        $paneRenderer = new PaneRenderer($pane);
        $this->assertTrue($paneRenderer->valid());
        ob_start();
        $paneRenderer->current();
        $res = ob_get_clean();
        $res = str_replace(array("\n", "\r"), '', $res);
        $this->assertEquals($expected, $res);
    }

    /**
     * @covers Flower\View\PaneRenderer::__toString
     */
    public function test__toStringSimple()
    {
        $builder =new Builder;
        $pane = $builder->build(array('wrapBegin' => 'foo', 'wrapEnd' => 'bar'));
        $paneRenderer = new PaneRenderer($pane);
        //$this->assertTrue($paneRenderer->valid());
        //$paneRenderer->current();
        $expected = '
<!-- begin PaneRenderer -->
foobar
<!-- end PaneRenderer -->
';
        $this->assertEquals(str_replace(array("\n","\r"),'', $expected), str_replace(array("\n","\r"), '', (string) $paneRenderer));
    }

}
