<?php
namespace FlowerTest\View\Pane;


use Flower\View\Pane\Builder;
use Flower\View\Pane\ListPaneFactory;
use Flower\View\Pane\PaneRenderer;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-01 at 22:15:54.
 */
class ListPaneFunctionalTest extends \PHPUnit_Framework_TestCase
{

    protected $builder;

    public function setUp()
    {
        $this->builder = new Builder(array('pane_class' => 'Flower\View\Pane\ListPane'));
    }

    public function testNoContents()
    {
        $expected =
'<!-- begin Renderer -->


<!-- end Renderer -->
';
        $expected = str_replace("\r\n","\n", $expected);
        $paneConfig = array(
            'id' => 'foo',
            'var' => '', //コンテンツをキャンセル
        );
        $pane = ListPaneFactory::factory($paneConfig, $this->builder);
        $renderer = new PaneRenderer($pane);
        //コンテンツなし
        $this->assertEquals($expected, str_replace("\r\n","\r", (string) $renderer));
    }

    public function testSimple()
    {
        $expected =
'<!-- begin Renderer -->
<ul>
<li>
  <span id="foo">
  <!-- start content content -->
    <!-- var content is not found -->
  <!-- end content content -->
  </span>
</li>
</ul>
<!-- end Renderer -->
';
        $expected = str_replace("\r\n","\n", $expected);
        $paneConfig = array(
            'id' => 'foo',
            'var' => 'content', //コンテンツをキャンセル
        );
        $pane = ListPaneFactory::factory($paneConfig, $this->builder);
        $renderer = new PaneRenderer($pane);
        //コンテンツなし
        $this->assertEquals($expected, str_replace("\r\n","\r", (string) $renderer));
    }

    public function testSimpleInner()
    {
        $expected =
'<!-- begin Renderer -->
<ul>
<li>
  <span class="container">
  <!-- start content foo -->
    <!-- var foo is not found -->
  <!-- end content foo -->
  </span>
<ul>
  <!-- start content CallbackRender -->
  <li>
  <span class="main">
  <!-- start content content -->
foo
  <!-- end content content -->
  </span>
  </li>
  <!-- end content CallbackRender -->
</ul>
</li>
</ul>
<!-- end Renderer -->
';
        $expected = str_replace("\r\n","\n", $expected);
        $paneConfig = array(
            'classes' => 'container',
            'var' => 'foo',
            'inner' => array(
                'classes' => 'main',
                'var' => 'content',
            ));
        $pane = $this->builder->build($paneConfig);
        $this->assertInstanceOf('Flower\View\Pane\ListPane', $pane);
        $renderer = new PaneRenderer($pane);
        $renderer->setVar('content', 'foo');
        $this->assertEquals($expected, str_replace("\r\n","\n", (string) $renderer));
    }

    public function testMultiInner()
    {
        $expected =
'<!-- begin Renderer -->
<ul>
<li>
  <span class="container">
  <!-- start content level0 -->
-0-
  <!-- end content level0 -->
  </span>
<ul>
  <li>
    <span class="main">
    <!-- start content level1 -->
-1-
    <!-- end content level1 -->
    </span>
  <ul>
    <li>
      <span class="main">
      <!-- start content level2 -->
-2-
      <!-- end content level2 -->
      </span>
    <ul>
      <!-- start content CallbackRender -->
      <li>
      <span class="sub">
      <!-- start content level3 -->
        <!-- var level3 is not found -->
      <!-- end content level3 -->
      </span>
      </li>
      <!-- end content CallbackRender -->
    </ul>
    </li>
  </ul>
  </li>
</ul>
</li>
</ul>
<!-- end Renderer -->
';
        $expected = str_replace("\r\n","\n",  $expected);
        $paneConfig = array(
            'classes' => 'container',
            'var' => 'level0',
            'inner' => array(
                'classes' => 'main',
                'var' => 'level1',
                'inner' => array(
                    'classes' => 'main',
                    'var' => 'level2',
                    'inner' => array(
                        'classes' => 'sub',
                        'var' => 'level3',
                    ),
                ),
            ),
        );
        $pane = $this->builder->build($paneConfig);
        $this->assertInstanceOf('Flower\View\Pane\ListPane', $pane);
        $renderer = new PaneRenderer($pane);
        $renderer->setVar('level0', '-0-');
        $renderer->setVar('level1', '-1-');
        $renderer->setVar('level2', '-2-');
        $this->assertEquals($expected, str_replace("\r\n","\n", (string) $renderer));
    }

    public function testMultiInnerCommentOff()
    {
        $expected = '<ul><li><span class="container">foo</span><ul><li><span class="main"><a href="foo">bar</a></span><ul><li><span class="main">foo</span></li></ul></li></ul></li></ul>';
        $paneConfig = array(
            'classes' => 'container',
            'var' => 'content',
            'inner' => array(
                'classes' => 'main',
                'var' => 'anchor',
                'inner' => array(
                    'classes' => 'main',
                    'var' => 'content',
                ),
            ),
        );
        $pane = $this->builder->build($paneConfig);
        $this->assertInstanceOf('Flower\View\Pane\ListPane', $pane);
        $renderer = new PaneRenderer($pane);
        $renderer->commentEnable = false;
        $renderer->indent = "";
        $renderer->linefeed = "";
        $renderer->setVar('content', 'foo');
        $renderer->setVar('anchor', '<a href="foo">bar</a>');
        $this->assertEquals($expected, (string) $renderer);
    }
}
