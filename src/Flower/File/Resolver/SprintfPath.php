<?php

namespace Flower\File\Resolver;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Traversable;
use Zend\Stdlib\SplStack;

use Flower\Exception;
use Flower\File\FileInfo;
use Flower\NamedFiles;

/**
 * Resolves view scripts based on a stack of paths
 */
class SprintfPath implements ResolverInterface, ResolveTerminatorInterface
{
    use MayTerminateTrait;

    const FAILURE_INVALID_PATH  = 'SprintfPath_Failure_No_Paths';
    const FAILURE_NOT_FOUND = 'SprintfPath_Failure_Not_Found';

    /**
     * Default suffix to use
     *
     * Appends this suffix if the template requested does not use it.
     *
     * @var string
     */
    protected $defaultSuffix = 'php';

    /**
     * @var string|object has __toString
     */
    protected $param;

    /**
     * @var string
     */
    protected $pathSpec;

    /**
     *
     * @var string
     */
    protected $path;

    /**
     * Reason for last lookup failure
     *
     * @var false|string
     */
    protected $lastLookupFailure = false;

    /**
     * Flag indicating whether or not LFI protection for parsing scripts is enabled
     * @var bool
     */
    protected $lfiProtectionOn = true;

    /**
     * Constructor
     *
     * @param  null|array|Traversable $options
     */
    public function __construct($options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Configure object
     *
     * @param  array|Traversable $options
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function setOptions($options)
    {
        if (!is_array($options) && !$options instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected array or Traversable object; received "%s"',
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }

        foreach ($options as $key => $value) {
            switch (strtolower($key)) {
                case 'lfi_protection':
                    $this->setLfiProtection($value);
                    break;
                case 'path_spec':
                    $this->setPathSpec($value);
                    break;
                case 'param':
                    $this->setParam($value);
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Set default file suffix
     *
     * @param  string $defaultSuffix
     * @return TemplatePathStack
     */
    public function setDefaultSuffix($defaultSuffix)
    {
        $this->defaultSuffix = (string) $defaultSuffix;
        $this->defaultSuffix = ltrim($this->defaultSuffix, '.');
        return $this;
    }

    /**
     * Get default file suffix
     *
     * @return string
     */
    public function getDefaultSuffix()
    {
        return $this->defaultSuffix;
    }

    /**
     * Add path spec ex.
     *  '/var/www/public_html/%s/tmp/data'
     *
     * @param  string $paths
     */
    public function setPathSpec($pathSpec)
    {
        $this->pathSpec = $pathSpec;
    }

    public function setParam($param)
    {
        $this->param = $param;
    }

    public function getPath()
    {
        if (isset($this->path)) {
            return $this->path;
        }

        if (!isset($this->pathSpec) || !isset($this->param)){
            throw new Exception\RuntimeException('path spec is not configured');
        }
        $this->path = $this->normalizePath(sprintf($this->pathSpec, $this->param));

        return $this->path;
    }

    /**
     * Normalize a path for insertion in the stack
     *
     * @param  string $path
     * @return string
     */
    public static function normalizePath($path)
    {
        return rtrim($path, '/\\') . DIRECTORY_SEPARATOR;
    }

    /**
     * Set LFI protection flag
     *
     * @param  bool $flag
     * @return TemplatePathStack
     */
    public function setLfiProtection($flag)
    {
        $this->lfiProtectionOn = (bool) $flag;
        return $this;
    }

    /**
     * Return status of LFI protection flag
     *
     * @return bool
     */
    public function isLfiProtectionOn()
    {
        return $this->lfiProtectionOn;
    }

    /**
     * Retrieve the filesystem path to a view script
     *
     * @param  string $name
     * @param  null|Flower\File\Spec\Resolver\ResolveSpecInterface $spec
     * @return string
     * @throws Exception\DomainException
     */
    public function resolve($name, $spec = null)
    {
        $this->lastLookupFailure = false;

        if ($this->isLfiProtectionOn() && preg_match('#\.\.[\\\/]#', $name)) {
            throw new Exception\DomainException(
                'Requested scripts may not include parent directory traversal ("../", "..\\" notation)'
            );
        }

        $path = $this->getPath();
        if (!is_dir($path)) {
            $this->lastLookupFailure = static::FAILURE_INVALID_PATH;
            return false;
        }

        if (isset($spec)) {
            $extensions = $spec->getExtensions();
        }
        else {
            $extensions = array($this->getDefaultSuffix());
        }
        //書き込みモードで、拡張子が指定されていない場合は、defaultSuffixを使う。
        //$specは書き込みモードのときに、getExtensionsで
        //引数で指定された拡張子か、デフォルト拡張子だけを返す。
        //
        //名前にドットを含むべきではないという設定にするかどうか。
        //クリーニングを検討した方がよさそう。
        //てか、specで拡張子指定があれば、そのまま解決すればいいよね。
        //

        $fileNames = array();
        foreach($extensions as $extension) {
            if (pathinfo($name, PATHINFO_EXTENSION) != $extension) {
                $fileNames[] = $name . '.' . $extension;
            }
            else {
                $fileNames[] = $name;
            }
        }

        $files = array();

        foreach ($fileNames as $fileName) {
            $file = new FileInfo($path . $fileName);
            if (null === $spec) {
                //すべて検索
                $files[] = $file;
                continue;
            }
            //キャッシュが使えるのでとにかくすべて検索
            if ($spec->isValid($file)) {
                $files[] = $file;
            }
        }

        if (count($files)) {
            return $files;
        }

        $this->lastLookupFailure = static::FAILURE_NOT_FOUND;
        return false;
    }

    /**
     * Get the last lookup failure message, if any
     *
     * @return false|string
     */
    public function getLastLookupFailure()
    {
        return $this->lastLookupFailure;
    }
}
