<?php
namespace Flower\File\Spec\Cache;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Flower\File\Spec\Cache\CacheSpecInterface;
use Flower\File\FileInfo;
use Flower\File\Event;
use Flower\File\Serializer\SimpleSerializer;
use Flower\File\Serializer\SerializerInterface;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

use Zend\Stdlib\ResponseInterface;
use Zend\Stdlib\Response;
/**
 *
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class DirectoryCacheSpec implements CacheSpecInterface, ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    protected $cacheEnabled;

    protected $cachePath;

    protected $serializer;

    /**
     *
     * @var FileInfo
     */
    protected $cacheFileInfo;

    /**
     * データがディレクトリの上位から継承するように構成してある場合に、
     * 下位ディレクトリのキャッシュを削除するためのスイッチ
     *
     * @var boolean
     */
    protected $cacheTreeEnabled = true;

    public function __construct($config = null)
    {
        if (is_array($config)) {
            $this->config = $config;
        }
        $this->serializer = new SimpleSerializer;
    }

    public function configure()
    {
        if (isset($this->config['cache_enabled'])) {
            $this->cacheEnabled = (boolean) $this->config['cache_enabled'];
        }

        if (isset($this->config['cache_path'])) {
            $this->cachePath = $this->config['cache_path'];
        }

        if (isset($this->config['name_dig'])) {
            $this->nameDig = (boolean) $this->config['name_dig'];
        }
    }

    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getSerializer()
    {
        return $this->serializer;
    }

    public function attach(EventManagerInterface $events)
    {

        /**
         * キャッシュデータが存在していれば、そのデータを返す
         *
         *
         */
        $this->listeners[] = $events->attach(Event::EVENT_CACHE_READ, [$this, 'onCacheRead']);

        /**
         * キャッシュが存在しない場合に、指定されたデータでキャッシュファイルを作成する
         *
         */
        $this->listeners[] = $events->attach(Event::EVENT_CACHE_MAKE, [$this, 'onCacheMake']);

        /**
         * データ書き込みの最終工程で呼ばれる。
         * 手動でアップロードした際、このイベントを呼べばキャッシュを更新できる。
         *
         */
        $this->listeners[] = $events->attach(Event::EVENT_REFRESH, [$this, 'onRefresh']);

    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function cacheEnabled()
    {
        if (!isset($this->cacheEnabled)) {
            //notice! do not set $this->cacheEnabled automatically
            if ($this->getCacheExtension() && $this->getCachePath()) {
                return true;
            }

            return false;
        }

        return $this->cacheEnabled;
    }

    public function getCacheFileName($name, $withExtension = true)
    {
        if(preg_match('#\.\.[\\\/]#', $name)) {
            return;
        }

        if (!$this->getCachePath() || !$this->getCacheExtension() || ! $this->cacheEnabled()) {
            return;
        }

        if ($withExtension) {
            return $this->getCachePath() . $name . $this->getCacheExtension();
        }

        return $this->getCachePath() . $name;
    }

    public function getCacheExtension()
    {
        return '.cache.php';
    }

    public function getCachePath()
    {
        if (! isset($this->cachePath) || empty($this->cachePath)) {
            return null;
        }
        return rtrim($this->cachePath, '/\\') . DIRECTORY_SEPARATOR;
    }

    public function onCacheRead(Event $e)
    {
        if (! $this->cacheEnabled()) {
            return null;
        }

        //キャッシュファイルを探索、存在していれば読み込んで返す

        $name = $e->getDataName();
        if (!strlen($name)) {
            return null;
        }

        $fileInfo = new FileInfo($this->getCacheFileName($name));

        if ($fileInfo->isFile() && $fileInfo->isReadable() && ($fileInfo->getSize() > 0)) {
            //retrieve cached data
            $serialized = file_get_contents($fileInfo->getPathname());
            //serialized data was written with test.
            $data = $this->getSerializer()->unserialize($serialized);

            $e->setData($data);
            /*
             * キャッシュに関する追加データが欲しい場合だが、通常は必要ないはず。
            $fileInfo->setSpecifiedExtension($this->getCacheExtension());
            $fileInfo->setValue($data);

            $namedFiles = $e->getNamedFiles();
            $namedFiles->setFile($fileInfo, $this->getCacheExtension());
             */
            return $data;
        }
        return null;
    }


    public function onCacheMake(Event $e)
    {
        if (! $this->cacheEnabled()) {
            return null;
        }

        $name = $e->getDataName();
        if (!strlen($name)) {
            return null;
        }

        $data = $e->getData();

        $fileName = $this->getCacheFileName($name);

        $fileInfo = new FileInfo($fileName);

        $pathInfo = $fileInfo->getPathInfo();
        do {
            //symlinkの場合でもリンク先がDirectoryであれば、true
            if ($pathInfo->isDir()) {
                if ($pathInfo->isWritable()) {
                    break;
                }
            }
            else {
                $cacheBasePath = new FileInfo($this->getCachePath());
                if (! $cacheBasePath->isWritable()) {
                    trigger_error('cache path is not writable :' . $this->getCachePath(), E_USER_WARNING);
                    return null;
                }
                $permission = $cacheBasePath->getPerms();
                mkdir($pathInfo->getPathname(), $permission, true);
            }
        } while (false);


        if ($this->getSerializer()->test($data)) {
            $serialized = $this->getSerializer()->serialize($data);
            return file_put_contents($fileName, $serialized, LOCK_EX);
        }

    }
    /**
     * 指定された名前以下の、キャッシュ拡張子を持つファイルを抹消する。
     * これにより上下関係の依存については最新に保てる。
     * 横方向の依存関係は手動で解決するべきである。
     *
     * 汎用的なタグの実装は保守コストを考え、使わない。
     * タグを使うならZend\Cacheなみのメタデータ管理が必要になり、
     * 安全性・拡張性・利用方法などを考えると自前で用意する必要はない。
     * タグなどを使いたければ、Zend\Cacheの利用を検討すること。
     *
     * 依存関係はsymlinkでも張ることができるが、
     * 複雑な依存関係の解決は不可能
     * データをマージしたいときは、マージ先のシンボルをphpファイルで読み込む。
     *
     * symlinkすることで、キャッシュの連鎖削除が可能になる。
     * 循環しないようにキャッシュファイルの削除ログファイルを管理する。
     * しかし、再帰検索の深さについては限度を指定すること。
     *
     * Zend\Cacheを使うメリットとしては、フロントエンドにデータを提供
     * もしくは、複数のサーバーへのディプロイの役目を果たさせることだが
     * メモリも無限ではないから必ずしもよい手とは言えない。
     *
     * @param \Flower\File\Event $e
     */
    public function onRefresh(Event $e)
    {
        $name = $e->getDataName();

        if (null === $name) {
            $name = '';
        }

        if (strlen($name)) {
            set_error_handler([$this, 'handleError'], E_WARNING);
            $filename = realpath($this->getCacheFileName($name));
            if (is_file($filename)) {
                unlink($filename);
            }

            restore_error_handler();
        }

        if ($this->cacheTreeEnabled) {
            if (strlen($name)) {
                $dir = $this->getCacheFileName($name, false);
                if (!is_dir($dir)) {
                    return null;
                }
            }
            else {
                $dir = $this->getCachePath();
            }

            $directoryIterator = new \RecursiveDirectoryIterator($dir);
            $rii = new \RecursiveIteratorIterator($directoryIterator);

            $cacheExtension = $this->getCacheExtension();

            set_error_handler([$this, 'handleError'], E_WARNING);
            foreach ($rii as $entry) {
                if ($entry->isFile() && strpos($entry->getBasename(), $cacheExtension)) {
                    unlink($entry->getPathname());
                }
            }
            restore_error_handler();
        }
    }

    public function handleError($errno, $errstr, $errfile, $errline)
    {
        if (strstr($errstr,'No such file or directory')) {
            //resume
            error_log($errstr . ' in '. $errfile. ':' . $errline, E_USER_NOTICE);
            return true;
        }
        throw new \Exception($errstr);
        throw new \Exception($errno . ': ' . $errstr . ' in ' . $errfile . ' : ' . $errline);
    }
}
