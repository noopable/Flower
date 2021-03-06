<?php

namespace FlowerTest\File;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

use Flower\File\Event;
use FlowerTest\Bootstrap;

use Flower\File\Spec\Resolver\Tree;
use Flower\File\Spec\Cache\DirectoryCacheSpec;


/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-04-05 at 22:30:00.
 */
class GatewayFunctionalTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Gateway
     */
    protected $object;

    protected $fileName;

    protected $configKey = 'test_flower_file';

    protected $multi = Array (
            'name' => 'multi.json',
            'section' => Array (
                'key' => 'value',
            ),
            'section2' => Array (
                'key2' => 'value2',
            ),
            'section_json' => Array (
                'key' => 'value',
            ),
        );
    protected $level1_level2 = array (
            /**
             * commonに注目すると、
             * level1.iniで指定してある値をlevel1/level2.iniで上書きしていることがわかる
             */
            'common' => array (
                'foo' => 'baz',
                'bar' => 'dummy',
            ),
            'level1' => Array (
                'key' => 'value',
                'name' => 'level1.ini',
            ),
            'level2' => Array (
                'key' => 'value',
                'name' => 'level2.ini',
            ),
        );


    protected $cachePath;

    protected $dataPath;

    protected $tmpPath;

    protected $cacheExtension = '.cache.php';

    /**
     *
     * Flower\File\Spec\Resolver::defaultExtension
     */
    protected $defaultExtension = '.default.php';
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();
        $reflection = new \ReflectionClass('Flower\File\Service\FileServiceFactoryFromConfig');
        $factory = $reflection->newInstance();
        $property = $reflection->getProperty('configKey');
        $property->setAccessible(true);
        $property->setValue($factory, $this->configKey);

        $this->object = $factory->createService($serviceManager);

        $this->config = include realpath(__DIR__ . '/../../config/autoload/file.config.php');

        $this->fileName = realpath(__DIR__ . '/TestAsset/data/sample.php');
        $this->cachePath = realpath(__DIR__ . '/TestAsset/data/cache/');
        $this->dataPath = realpath(__DIR__ . '/TestAsset/data/');
        $this->tmpPath = realpath(__DIR__ . '/TestAsset/tmp/');
        $this->pathStackPath = realpath($this->config['test_flower_file']['resolve_spec_options']['path_stack']['flower']);

    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $directoryIterator = new \RecursiveDirectoryIterator($this->cachePath);
        $rii = new \RecursiveIteratorIterator($directoryIterator);

        $cacheExtension = $this->cacheExtension;

        foreach ($rii as $entry) {
            if ($entry->isFile() && strpos($entry->getBasename(), $cacheExtension)) {
                unlink($entry->getPathname());
            }
        }

        $directoryIterator = new \RecursiveDirectoryIterator($this->dataPath);
        $rii = new \RecursiveIteratorIterator($directoryIterator);

        $cacheExtension = $this->defaultExtension;

        foreach ($rii as $entry) {
            if ($entry->isFile() && strpos($entry->getFilename(), $cacheExtension)) {
                unlink($entry->getPathname());
            }
        }

        $directoryIterator = new \RecursiveDirectoryIterator($this->dataPath);
        $rii = new \RecursiveIteratorIterator($directoryIterator);

        $cacheExtension = $this->defaultExtension;

        foreach ($rii as $entry) {
            if ($entry->isFile() && strpos($entry->getFilename(), $cacheExtension)) {
                unlink($entry->getPathname());
            }
        }

        $directoryIterator = new \RecursiveDirectoryIterator($this->tmpPath);
        $rii = new \RecursiveIteratorIterator($directoryIterator);

        $cacheExtension = $this->defaultExtension;

        foreach ($rii as $entry) {
            if ($entry->isFile() && strpos($entry->getFilename(), $cacheExtension)) {
                unlink($entry->getPathname());
            }
        }
    }

    public function testFactory()
    {
        $this->assertInstanceof('Flower\File\Gateway\Gateway', $this->object);
    }

    /**
     * @covers Flower\File\Gateway\Gateway::resolveAll
     * @todo   Implement testResolveAll().
     */
    public function testResolveAll()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function testIssetTreeResolveSpec()
    {
        $events = $this->object->getEventManager();
        $listeners = $events->getListeners(Event::EVENT_RESOLVE);
        $this->assertGreaterThan(0, $listeners->count());
        $found = false;
        foreach ($listeners as $listener) {
            if ($listener instanceof \Zend\Stdlib\CallbackHandler) {
                $callback = $listener->getCallback();
                if (is_array($callback)) {
                    $method = end($callback);
                    $object = reset($callback);
                    if ($object instanceof Tree) {
                        //getCallbackはWeakRefも展開する
                        $found = true;
                        break;
                    }
                    else {
                        $this->assertTrue($object);
                    }
                }
            }
            else {
                $this->assertTrue($listener);
            }
        }
        $this->assertTrue($found);
        return $object;
    }

    public function testIssetDirectoryCacheSpec()
    {
        $events = $this->object->getEventManager();
        $listeners = $events->getListeners(Event::EVENT_CACHE_READ);
        $this->assertGreaterThan(0, $listeners->count());
        $found = false;
        foreach ($listeners as $listener) {
            if ($listener instanceof \Zend\Stdlib\CallbackHandler) {
                $callback = $listener->getCallback();
                if (is_array($callback)) {
                    $method = end($callback);
                    $object = reset($callback);
                    if ($object instanceof DirectoryCacheSpec) {
                        //getCallbackはWeakRefも展開する
                        $found = true;
                        break;
                    }
                    else {
                        $this->assertTrue($object);
                    }
                }
            }
            else {
                $this->assertTrue($listener);
            }
        }
        $this->assertTrue($found);
        $this->assertEquals($this->cachePath, realpath($object->getCachePath()));

        return $object;
    }



    /**
     *
     * @depends testIssetTreeArrayResolveSpec
     * @param type $listener
     */
    public function testIssetAggregateResolverInSpec($listener)
    {
        $resolver = $listener->getResolver();
        $this->assertInstanceof('Flower\File\Resolver\AggregateResolver', $resolver);
        return $resolver;
    }
        /**
     *
     * @depends testIssetAggregateResolverInSpec
     * @param type $aggregateResolver
     */
    public function testAggregateResolverHasPathStackResolverInSpec($aggregateResolver)
    {
        $this->assertInstanceof('Flower\File\Resolver\AggregateResolver', $aggregateResolver);
        $iterator = $aggregateResolver->getIterator();
        $found = false;
        foreach ($iterator as $resolver) {
            if ($resolver instanceof \Flower\File\Resolver\PathStack) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        return $resolver;
    }

    /**
     *
     * @depends testAggregateResolverHasPathStackResolverInSpec
     * @param type $resolver
     */
    public function testPathStackResolverHasPathStackInSpec($resolver)
    {
        $this->assertInstanceof('Flower\File\Resolver\PathStack', $resolver);
        $paths = $resolver->getPaths()->toArray();
        $path = realpath(end($paths));
        $expected = $this->pathStackPath;
        $this->assertEquals($expected, $path);
    }

    /**
     *
     * @covers Flower\File\Gateway\Gateway::resolve
     */
    public function testResolveWithEvent()
    {
        $event = $this->object->getEvent('sample');
        $events = $this->object->getEventManager();

        $res = $this->object->resolve($event, Event::RESOLVE_READ);

        $namedFiles = $event->getNamedFiles();

        /**
         * メソッドのレスポンスとイベント内namedFilesは同一インスタンス
         */
        $this->assertSame($res, $namedFiles);

        $this->assertNotEquals(0, $namedFiles->count());


        $fileInfo = $namedFiles->getFile();

        $this->assertEquals($this->fileName, $fileInfo->getRealPath());
        return $fileInfo->getRealPath();
    }

    /**
     *
     * @covers Flower\File\Gateway\Gateway::resolve
     */
    public function testResolveWidthName()
    {
        $res = $this->object->resolve('sample', Event::RESOLVE_READ);

        $this->assertInstanceof('Flower\File\NamedFiles', $res);
        $fileInfo = $res->getFile();

        $this->assertEquals($this->fileName, $fileInfo->getRealPath());
        return $fileInfo->getRealPath();
    }

    public function testNoexistsRead()
    {
        $data = $this->object->read('no_exists');
        $this->assertNull($data);
    }
    /**
     *
     * @covers Flower\File\Gateway\Gateway::read
     */
    public function testRead()
    {
        $fileData = include $this->fileName;
        $data = $this->object->read('sample');
        $this->assertEquals($fileData, $data);
    }

    /**
     * multiという名前のmulti.ini multi.jsonを読み込んでマージしている
     *
     */
    public function testMultiRead()
    {
        $data = $this->object->read('multi');
        $this->assertEquals($this->multi, $data);
    }

    public function testIsMadeCache()
    {
        $this->object->read('multi');
        $this->assertFileExists($this->cachePath . DIRECTORY_SEPARATOR . 'multi' . $this->cacheExtension);
    }

    public function testRefreshCache()
    {
        $this->object->read('multi');
        $cacheFile = $this->cachePath . DIRECTORY_SEPARATOR . 'multi' . $this->cacheExtension;
        $this->assertFileExists($cacheFile);
        $this->object->refresh('multi');
        $this->assertFileNotExists($cacheFile);
    }

    /**
     * 階層指定した読み込みでは、親ディレクトリの設定を読んで、
     * 子ディレクトリで読んだ内容をマージしている。
     * level1.ini
     * level1/level2.ini
     * の順
     */
    public function testHierarchicalRead()
    {
        $data = $this->object->read('level1/level2');
        $this->assertEquals($this->level1_level2, $data);
    }

    /**
     * 階層型のデータは、各階層でマージされた内容を
     * 各階層にキャッシュする。
     * これにより複雑な設定ファイルでも自動的にマージされるようになる。
     *
     */
    public function testHierarchicalMadeCache()
    {
        $this->object->read('level1/level2');
        $cacheFile1 = $this->cachePath . DIRECTORY_SEPARATOR . 'level1' . $this->cacheExtension;
        $cacheFile2 = $this->cachePath . DIRECTORY_SEPARATOR . 'level1/level2' . $this->cacheExtension;
        $this->assertFileExists($cacheFile1);
        $this->assertFileExists($cacheFile2);
    }

    public function testHierarchicalRefreshLeafOnly()
    {
        $this->object->read('level1/level2');
        $cacheFile1 = $this->cachePath . DIRECTORY_SEPARATOR . 'level1' . $this->cacheExtension;
        $cacheFile2 = $this->cachePath . DIRECTORY_SEPARATOR . 'level1/level2' . $this->cacheExtension;
        $this->assertFileExists($cacheFile1);
        $this->assertFileExists($cacheFile2);
        $this->object->refresh('level1/level2');
        $this->assertFileExists($cacheFile1);
        $this->assertFileNotExists($cacheFile2);
    }

    public function testHierarchicalRefreshRecursive()
    {
        $this->object->read('level1/level2');
        $cacheFile1 = $this->cachePath . DIRECTORY_SEPARATOR . 'level1' . $this->cacheExtension;
        $cacheFile2 = $this->cachePath . DIRECTORY_SEPARATOR . 'level1/level2' . $this->cacheExtension;
        $this->assertFileExists($cacheFile1);
        $this->assertFileExists($cacheFile2);
        $this->object->refresh('level1');
        $this->assertFileNotExists($cacheFile1);
        $this->assertFileNotExists($cacheFile2);
    }

    /**
     * @covers Flower\File\Gateway\Gateway::write
     */
    public function testWrite()
    {
        // Remove the following lines when you implement this test.
        $this->object->write('tmp',['foo' => 'bar']);
        $this->assertFileExists($this->dataPath . '/tmp' . $this->defaultExtension);
    }

        /**
     * @covers Flower\File\Gateway\Gateway::write
     */
    public function testWriteHierarchical()
    {
        // Remove the following lines when you implement this test.
        $this->object->write('tmp/tmp2',['foo' => 'bar']);
        $this->assertFileExists($this->dataPath . '/tmp/tmp2' . $this->defaultExtension);
    }

    /**
     * @covers Flower\File\Gateway\Gateway::namedFilesWrite
     * @todo   Implement testAggregateWrite().
     */
    public function testNamedFilesWrite()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * 存在しない名前のキャッシュをクリアする命令
     * unlinkが吐くWarningを握りつぶしたかどうかの確認
     *
     */
    public function testNoExistsRefresh()
    {
        $this->object->refresh('foo');
    }

    /**
     * Don't write this method in tearDown because this test has unlink method
     * if settings are wrong, this method causes a terrible result.
     * @depends testIssetDirectoryCacheSpec
     */
    public function testRefreshAll()
    {
        $this->object->refresh();
    }
}
