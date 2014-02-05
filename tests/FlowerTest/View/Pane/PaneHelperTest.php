<?php

/**
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace FlowerTest\View\Pane;

use Flower\View\Pane\PaneHelper;
use Flower\Test\TestTool;
use Zend\View\Renderer\PhpRenderer;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-04-04 at 21:39:36.
 */
class PaneHelperTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var PaneHelper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {

        $this->renderer = new PhpRenderer;
        $this->helper   = new PaneHelper;
        $this->helper->setView($this->renderer);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {

    }

    /**
     *
     */
    public function testTheSameRenderer()
    {
        $this->assertEquals(spl_object_hash($this->renderer), spl_object_hash($this->helper->getView()));
    }


    public function testRendererReceiveData()
    {
        $this->renderer->setVars(array('content' => 'This is Content test.'));
        $text = $this->renderer->vars('content');

        $this->assertEquals('This is Content test.', $text);
    }

    /**
     * ViewHelperManagerから取得する際、レンダラインスタンスが異なることがある
     * レンダラに設定したデータ（コントローラー等からの抽入データ）と
     * ヘルパーのビューが持っているデータが連携していることを確認する。
     *
     * @depends testTheSameRenderer
     */
    public function testRendererInHelperReceiveData()
    {
        $this->renderer->setVars(array('content' => 'This is Content test.'));
        $text = $this->helper->getView()->vars('content');

        $this->assertEquals('This is Content test.', $text);
    }

    /**
     * @covers Flower\View\Pane\PaneHelper::abstractFactoryPaneRenderer
     * @todo   Implement testAbstractFactoryPaneRenderer().
     */
    public function testAbstractFactoryPaneRenderer() {

        $paneConfig = include __DIR__ . '/TestAsset/2column.php';
        $pane = $this->helper->paneFactory($paneConfig);
        $renderer  = $this->helper->abstractFactoryPaneRenderer($pane);
        $renderer2 = $this->helper->abstractFactoryPaneRenderer($pane);
        //別インスタンスだが内容は同等
        //ペインの交互出力
        $this->assertEquals($renderer, $renderer2);
        $this->assertNotSame($renderer, $renderer2);
    }

    /**
     * @covers Flower\View\Pane\PaneHelper::paneFactory
     * @todo   Implement testPaneFactory().
     */
    public function testPaneFactory() {
        // Remove the following lines when you implement this test.
        $paneConfig = include __DIR__ . '/TestAsset/2column.php';
        $pane = $this->helper->paneFactory($paneConfig);
        $this->assertInstanceof('Flower\View\Pane\PaneClass\Pane', $pane);
    }

    /**
     * @covers Flower\View\Pane\PaneHelper::__invoke
     */
    public function test__invokeDefaultPane() {
        //default pane
        $expected = str_replace(array("\n", "\r"), '', '<!-- begin Renderer -->
<!-- start pane -->
  <!-- start content content -->
  <div class="container">
    <!-- var content is not found -->
  </div>
  <!-- end content content -->
<!-- end pane -->
<!-- end Renderer -->
');
        $res = $this->helper->__invoke();
        $this->assertInstanceOf('Flower\View\Pane\PaneRenderer', $res);
        $this->assertEquals($expected, str_replace(array("\n", "\r"), '', (string) $res));
    }

    /**
     * @covers Flower\View\Pane\PaneHelper::__invoke
     */
    public function test__invokeWithPaneConfig() {
        $paneConfig = array(
            'tag' => 'span',
            'inner' => array(
                'classes' => 'container foo',
                'pane_class' => 'FlowerTest\View\Pane\TestAsset\YetAnotherPane',
            ),
        );
        //default pane
        $expected = str_replace(array("\n", "\r"), '',
'
<!-- begin Renderer -->
<span>
  <!-- start content content -->
  <div class="container foo">
    <!-- var content is not found -->
  </div>
  <!-- end content content -->
</span>
<!-- end Renderer -->
');
        $res = $this->helper->__invoke($paneConfig);
        $this->assertInstanceOf('Flower\View\Pane\PaneRenderer', $res);
        $this->assertEquals($expected, str_replace(array("\n", "\r"), '', (string) $res));
    }

    public function testDefaultPane()
    {
        $data = $this->helper->__invoke();
        $expected = <<<EOF
<!-- begin Renderer -->
<!-- start pane -->
  <!-- start content content -->
  <div class='container'>
    <!-- var content is not found -->
  </div>
  <!-- end content content -->
<!-- end pane -->

<!-- end Renderer -->

EOF;
        $this->assertXmlStringEqualsXmlString($expected, (string) $data);
    }

    /**
     * 現在のヘルパーはコールするたびにPaneRendererを返す
     * ヘルパーを複数回コールすれば同じ結果が得られる。
     *
     */
    public function testCallTwiceDefaultPane()
    {
        $expected = <<<EOF
<!-- begin Renderer -->
<!-- start pane -->
  <!-- start content content -->
  <div class='container'>
    <!-- var content is not found -->
  </div>
  <!-- end content content -->
<!-- end pane -->

<!-- end Renderer -->

EOF;
        $data = $this->helper->__invoke();
        $data2 = $this->helper->__invoke();
        $this->assertXmlStringEqualsXmlString($expected, (string) $data);
        $this->assertXmlStringEqualsXmlString($expected, (string) $data2);
    }

    /**
     * ペインがキューで作られていると、本来キューは1度しかイテレーションされない。
     * そのため、2度同じペインをレンダリングすると次回はペインデータを失ってしまう。
     * キューの動作としては正しいが、ペインが1度だけ出力される必然性は必ずしもない。
     *
     *
     */
    public function testRenderTwiceDefaultPane()
    {
        $expected = <<<EOF
<!-- begin Renderer -->
<!-- start pane -->
  <!-- start content content -->
  <div class='container'>
    <!-- var content is not found -->
  </div>
  <!-- end content content -->
<!-- end pane -->

<!-- end Renderer -->

EOF;
        $data = $this->helper->__invoke();
        $this->assertXmlStringEqualsXmlString($expected, (string) $data);
        $this->assertXmlStringEqualsXmlString($expected, (string) $data);
    }

    public function testRendererRecieveData()
    {
        $this->renderer->setVars(array('content' => 'This is content test'));
        $this->assertEquals('This is content test', $this->renderer->get('content'));
    }

    public function testDefaultPaneWithData()
    {
        $this->renderer->setVars(array('content' => 'This is Content test'));
        $data = $this->helper->__invoke();
        $expected = <<<EOF
<!-- begin Renderer -->
<!-- start pane -->
  <!-- start content content -->
  <div class='container'>
This is Content test
  </div>
  <!-- end content content -->
<!-- end pane -->

<!-- end Renderer -->

EOF;
        $this->assertXmlStringEqualsXmlString($expected, (string) $data);
    }

    public function testSpecifiedPane()
    {
        $pane = include __DIR__ . '/TestAsset/2column.php';
        $this->renderer->setVars(array('content' => 'This is Content test'));
        $data = $this->helper->__invoke($pane);
        $expected = <<<EOF
<!-- begin Renderer -->
<body class='container'>
  <!-- start content header -->
  <div id='overview' class='container subhead header'>
    <!-- var header is not found -->
  </div>
  <!-- end content header -->
  <div class='container'>
    <!-- start content content -->
    <div class='article row pull-right'>
This is Content test
    </div>
    <!-- end content content -->
    <div style="float:right;" id="sidebar" class="sidebar">
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

EOF;
        $this->assertXmlStringEqualsXmlString($expected, (string) $data);
    }

    /**
     * PaneHelperに登録されたオブジェクトキーによりデータとしてpaneを
     * injectすることができる。
     *
     */
    public function testPaneFromData()
    {
        $pane = include __DIR__ . '/TestAsset/2column.php';
        $vars = array(
                'content' => 'This is Content test',
                'pane' => $pane,
            );
        $this->renderer->setVars($vars);
        $data = $this->helper->__invoke($pane);
        $expected = <<<EOF
<!-- begin Renderer -->
<body class='container'>
  <!-- start content header -->
  <div id='overview' class='container subhead header'>
    <!-- var header is not found -->
  </div>
  <!-- end content header -->
  <div class='container'>
    <!-- start content content -->
    <div class='article row pull-right'>
This is Content test
    </div>
    <!-- end content content -->
    <div style="float:right;" id="sidebar" class="sidebar">
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

EOF;
        $this->assertXmlStringEqualsXmlString($expected, (string) $data);
    }

    /**
     * @covers Flower\View\Pane\PaneHelper::setObjectKey
     */
    public function testSetObjectKey() {
        $this->helper->setObjectKey('foo');
        $this->assertEquals('foo', TestTool::getPropertyValue($this->helper, 'objectKey'));
    }

    /**
     * @covers Flower\View\Pane\PaneHelper::getObjectKey
     */
    public function testGetObjectKey() {
        $this->helper->setObjectKey('foo');
        $this->assertEquals('foo', $this->helper->getObjectKey());
    }

}
