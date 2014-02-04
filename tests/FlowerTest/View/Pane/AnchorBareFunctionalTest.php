<?php
namespace FlowerTest\View\Pane;

use Flower\Test\TestTool;
use Flower\View\Pane\Builder;
use Flower\View\Pane\AnchorPaneFactory;
use Flower\View\Pane\ListRenderer;
use FlowerTest\Bootstrap;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Renderer\PhpRenderer;

/**
 * rendering文字列中に下記が現れる場合
* No RouteStackInterface instance provided
*  Urlヘルパーにrouterがセットされていない場合
* No RouteMatch instance provided
*  RouteMatchが設定されていないがルート名が指定されていない場合
* RouteMatch does not contain a matched route name
*  RouteMatchにmatchedRouteNameが設定されていない場合
*
* ※いずれもMvcフローから呼ばれていない可能性が高い
*
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-01 at 22:15:54.
 */
class AnchorBareFunctionalTest extends \PHPUnit_Framework_TestCase
{
    protected $serviceLocator;

    protected $router;

    protected $helper;

    protected $view;

    public function setUp()
    {
        $config = Bootstrap::getServiceManager()->get('ApplicationConfig');
        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();
        $serviceManager->get('Application')->bootstrap();
        $this->serviceLocator = $serviceManager;
        $this->router = $this->serviceLocator->get('HttpRouter');
        $this->helperManager = $this->serviceLocator->get('ViewHelperManager');
        $this->view = new PhpRenderer;
        $this->view->setHelperPluginManager($this->helperManager);
        $this->helper = $this->view->plugin('npNavi');
    }

    public function testEnv()
    {
        $renderer = $this->helperManager->getRenderer();
        $this->assertInstanceOf('Zend\View\Renderer\PhpRenderer', $renderer);

        $this->assertInstanceOf('Flower\View\Pane\AnchorHelper', $this->helper);

        $builder = $this->helper->getBuilder();
        $this->assertEquals('Flower\View\Pane\Anchor', TestTool::getPropertyValue($builder, 'paneClass'));
    }

    public function testSimple()
    {
        $expected =
'<!-- begin ListRenderer -->


<!-- end ListRenderer -->
';
        $expected = str_replace("\r\n", "\n", $expected);
        $paneConfig = array(
            'id' => 'foo',
        );
        $pane = AnchorPaneFactory::factory($paneConfig, (new Builder));
        $renderer = new ListRenderer($pane);
        $renderer->setView((new PhpRenderer));
        //__toString() must not throw an exception
        //foreach ($renderer as $entry) {}
        $this->assertEquals($expected, str_replace("\r\n", "\n", (string) $renderer));
    }

    /**
     * No RouteStackInterface instance provided
     * Routerが設定されていない場合にhrefがこのように設定されます。
     *
     */
    public function testSimpleInnerWithoutRouter()
    {

        $expected =
'<!-- begin ListRenderer -->
<ul>
<li>
<a href="No%20RouteStackInterface%20instance%20provided">
foo</a>
<ul>
  <!-- start content ListPane -->
  <li>
  <a href="No%20RouteStackInterface%20instance%20provided">
content  </a>
  </li>
  <!-- end content ListPane -->
</ul>
</li>
</ul>
<!-- end ListRenderer -->
';
        $expected = str_replace("\r\n","\n", $expected);
        $paneConfig = array(
            'classes' => 'container',
            'var' => 'foo',
            'inner' => array(
                'classes' => 'main',
                'var' => 'content',
            ));
        $pane = (new Builder(array('pane_class' => 'Flower\View\Pane\Anchor')))->build($paneConfig);
        $this->assertInstanceOf('Flower\View\Pane\Anchor', $pane);
        $renderer = new ListRenderer($pane);
        $renderer->setView(new PhpRenderer);
        $this->assertEquals($expected, str_replace("\r\n","\n", (string) $renderer));
    }

    /**
     * No RouteStackInterface instance provided
     * Routerが設定されていない場合にhrefがこのように設定されます。
     *
     */
    public function testSimpleInnerWithoutRouterUsePagesInsteadofInner()
    {

        $expected =
'<!-- begin ListRenderer -->
<ul>
<li>
<a href="No%20RouteStackInterface%20instance%20provided">
foo</a>
<ul>
  <!-- start content ListPane -->
  <li>
  <a href="No%20RouteStackInterface%20instance%20provided">
content  </a>
  </li>
  <!-- end content ListPane -->
</ul>
</li>
</ul>
<!-- end ListRenderer -->
';
        $expected = str_replace("\r\n","\n", $expected);
        $paneConfig = array(
            'classes' => 'container',
            'var' => 'foo',
            'pages' => array(
                'classes' => 'main',
                'var' => 'content',
            ));
        $pane = (new Builder(array('pane_class' => 'Flower\View\Pane\Anchor')))->build($paneConfig);
        $this->assertInstanceOf('Flower\View\Pane\Anchor', $pane);
        $renderer = new ListRenderer($pane);
        $renderer->setView(new PhpRenderer);
        $this->assertEquals($expected, str_replace("\r\n","\n", (string) $renderer));
    }

    /**
     * No RouteStackInterface instance provided
     * Routerが設定されていない場合にhrefがこのように設定されます。
     *
     */
    public function testSimpleInnerWithoutRouterUsePagesAndInner()
    {

        $expected =
'<!-- begin ListRenderer -->
<ul>
<li>
<a href="No%20RouteStackInterface%20instance%20provided">
foo</a>
<ul>
  <!-- start content ListPane -->
  <li>
  <a href="No%20RouteStackInterface%20instance%20provided">
Label1  </a>
  </li>
  <!-- end content ListPane -->
  <!-- start content ListPane -->
  <li>
  <a href="No%20RouteStackInterface%20instance%20provided">
Label2  </a>
  </li>
  <!-- end content ListPane -->
</ul>
</li>
</ul>
<!-- end ListRenderer -->
';
        $expected = str_replace("\r\n","\n", $expected);
        $paneConfig = array(
            'classes' => 'container',
            'var' => 'foo',
            'inner' => array(
                'classes' => 'main',
                'var' => 'Label1',
                'order' => 10,
            ),
            'pages' => array(
                'classes' => 'main',
                'var' => 'Label2',
            ),);
        $pane = (new Builder(array('pane_class' => 'Flower\View\Pane\Anchor')))->build($paneConfig);
        $this->assertInstanceOf('Flower\View\Pane\Anchor', $pane);
        $renderer = new ListRenderer($pane);
        $renderer->setView(new PhpRenderer);
        $this->assertEquals($expected, str_replace("\r\n","\n", (string) $renderer));
    }

    /**
     * No RouteStackInterface instance provided
     * Routerが設定されていない場合にhrefがこのように設定されます。
     *
     */
    public function testSimpleInnerWithoutRouterUsePagesAndInnerMulti()
    {

        $expected =
'<!-- begin ListRenderer -->
<ul>
<li>
<a href="No%20RouteStackInterface%20instance%20provided">
foo</a>
<ul>
  <!-- start content ListPane -->
  <li>
  <a href="No%20RouteStackInterface%20instance%20provided">
Label1  </a>
  </li>
  <!-- end content ListPane -->
  <!-- start content ListPane -->
  <li>
  <a href="No%20RouteStackInterface%20instance%20provided">
Label2  </a>
  </li>
  <!-- end content ListPane -->
  <!-- start content ListPane -->
  <li>
  <a href="No%20RouteStackInterface%20instance%20provided">
Label3  </a>
  </li>
  <!-- end content ListPane -->
</ul>
</li>
</ul>
<!-- end ListRenderer -->
';
        $expected = str_replace("\r\n","\n", $expected);
        $paneConfig = array(
            'classes' => 'container',
            'var' => 'foo',
            'inner' => array(
                array(
                    'classes' => 'main',
                    'var' => 'Label1',
                    'order' => 20,
                ),
                    array(
                    'classes' => 'main',
                    'var' => 'Label2',
                    'order' => 10,
                )
            ),
            'pages' => array(
                'classes' => 'main',
                'var' => 'Label3',
            ),);
        $pane = (new Builder(array('pane_class' => 'Flower\View\Pane\Anchor')))->build($paneConfig);
        $this->assertInstanceOf('Flower\View\Pane\Anchor', $pane);
        $renderer = new ListRenderer($pane);
        $renderer->setView(new PhpRenderer);
        $this->assertEquals($expected, str_replace("\r\n","\n", (string) $renderer));
    }

    /**
     * No RouteStackInterface instance provided
     * Routerが設定されていない場合にhrefがこのように設定されます。
     *
     */
    public function testSimpleInnerWithoutRouterUsePagesAndInnerMulti2()
    {

        $expected =
'<!-- begin ListRenderer -->
<ul>
<li>
<a href="No%20RouteStackInterface%20instance%20provided">
foo</a>
<ul>
  <!-- start content ListPane -->
  <li>
  <a href="No%20RouteStackInterface%20instance%20provided">
Label1  </a>
  </li>
  <!-- end content ListPane -->
  <!-- start content ListPane -->
  <li>
  <a href="No%20RouteStackInterface%20instance%20provided">
Label2  </a>
  </li>
  <!-- end content ListPane -->
  <!-- start content ListPane -->
  <li>
  <a href="No%20RouteStackInterface%20instance%20provided">
Label3  </a>
  </li>
  <!-- end content ListPane -->
</ul>
</li>
</ul>
<!-- end ListRenderer -->
';
        $expected = str_replace("\r\n","\n", $expected);
        $paneConfig = array(
            'classes' => 'container',
            'var' => 'foo',
            'inner' => array(
                'classes' => 'main',
                'var' => 'Label3',
            ),
            'pages' => array(
                array(
                    'classes' => 'main',
                    'var' => 'Label1',
                    'order' => 20,
                ),
                    array(
                    'classes' => 'main',
                    'var' => 'Label2',
                    'order' => 10,
                )
            ),
        );
        $pane = (new Builder(array('pane_class' => 'Flower\View\Pane\Anchor')))->build($paneConfig);
        $this->assertInstanceOf('Flower\View\Pane\Anchor', $pane);
        $renderer = new ListRenderer($pane);
        $renderer->setView(new PhpRenderer);
        $this->assertEquals($expected, str_replace("\r\n","\n", (string) $renderer));
    }

    public function testSimpleInnerWithoutRouteMatch()
    {
        $expected =
'<!-- begin ListRenderer -->
<ul>
<li>
<a href="No%20RouteMatch%20instance%20provided">
foo</a>
<ul>
  <!-- start content ListPane -->
  <li>
  <a href="No%20RouteMatch%20instance%20provided">
content  </a>
  </li>
  <!-- end content ListPane -->
</ul>
</li>
</ul>
<!-- end ListRenderer -->
';
        $expected = str_replace("\r\n","\n", $expected);
        $paneConfig = array(
            'classes' => 'container',
            'var' => 'foo',
            'inner' => array(
                'classes' => 'main',
                'var' => 'content',
            ));

        $pane = $this->helper->paneFactory($paneConfig);
        $this->assertInstanceOf('Flower\View\Pane\Anchor', $pane);

        $renderer = $this->helper->__invoke($paneConfig);
        $this->assertEquals($expected, str_replace("\r\n","\n", (string) $renderer));
    }

}