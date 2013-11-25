<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\FilePostRedirectGet\Plugin;

use Zend\Filter\FilterInterface;
use Zend\Filter\File\RenameUpload;
use Zend\Http\Response;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\Exception\RuntimeException;
use Zend\Session\Container;
use Zend\Stdlib\ErrorHandler;
use Zend\Stdlib\ArrayUtils;

use Zend\Validator\File\UploadFile;
use Zend\Validator\ValidatorInterface;

use GlobIterator;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;


/**
 * ZF2標準のFilePRGでは、フォームを必須とするが、フォームのisValidを勝手に呼ぶ
 * ため、PRG後にデータ調整をしたい場合は不具合の原因になる。
 *
 * ここでは、統一tempフォルダに移動するだけの措置を行う。
 *
 * 各種追加の処理を行いたければ、フィルターを追加する、バリデーションを追加する
 * といったことが必要になるが、だとしたら、InputFilterを追加した方がいいのでは
 * ないか？
 *
 * PRGの処理では、真にアップロードされたファイルであるかどうかの確認は、
 * アップロードされたリクエスト時にのみわかるので、ポストデータとして、
 * アップロードされていないデータが渡るのは問題がある。
 *
 * この問題は、ZF2のFilePostRedirectGetでは、ポスト時のvalidationをそのまま
 * リダイレクト後に使うことで解消している。
 * しかしこの処理はフォームとの密結合を招き、フォーム処理の柔軟性を損ねる。
 *
 * FlowerのFilePostRedirectGetでは、フォームの関与を固定しない。
 * ファイルは、getFilesで受け取ることを前提とし、postデータにはキー文字列だけを渡す。
 * この構造は通常のFile要素のFileInputではエラーになるので専用の要素または
 * Inputを使うことになる。
 * 通常のFileInputで、FilePostRedirectGetからのデータを受け取った場合でも、
 * それで指定されたファイルがis_uploaded_fileにtrueで返すことはありえないから、
 * 通常のFileInputを使った要素があってそれを信頼したソースがあっても問題は生じない。
 * 単に動作しないだけである。
 *
 * FlowerのFilePostRedirectGetのデメリットは通常のFileInputが使えないので、
 * 専用のファイル要素を使うことになる点である。
 * しかし、プレビューを考慮する上ではどのみち専用の要素を使うことになるので、
 * 状況によっては、デメリットにはならない。
 *
 */
/**
 * Plugin to help facilitate Post/Redirect/Get for file upload forms
 * (http://en.wikipedia.org/wiki/Post/Redirect/Get)
 *
 * Requires that the Form's File inputs contain a 'fileRenameUpload' filter
 * with the target option set: 'target' => /valid/target/path'.
 * This is so the files are moved to a new location between requests.
 * If this filter is not added, the temporary upload files will disappear
 * between requests.
 */
class FilePostRedirectGet extends AbstractPlugin implements ValidatorInterface
{
    /**
     * @var Container
     */
    protected $sessionContainer;

    /**
     *
     * @var UploadFile
     */
    protected $uploadFileValidator;

    /**
     *
     * @var RenameUpload
     */
    protected $renameFilter;

    protected $thumbnailFilter;

    protected $errorMessages = array();

    protected $defaultRenameFilterOptions = array(
        'overwrite'       => true,
        'use_upload_name' => false,
        'randomize' => true,
    );

    protected $requestKey = 'fprg';

    protected $tmpDir;

    protected $tmpBaseDir;

    protected $tmpDirPolicy = 'daily';

    /**
     *
     * @var string
     */
    protected $previewRoute;

    /**
     *
     * @var array
     */
    protected $previewParams;
    /**
     * @param  FormInterface $form
     * @param  string        $redirect      Route or URL string (default: current route)
     * @param  bool          $redirectToUrl Use $redirect as a URL string (default: false)
     * @return bool|array|Response
     */
    public function __invoke()
    {
        return $this;
    }

    public function handle($redirect = null, $redirectToUrl = false)
    {
        $request = $this->getController()->getRequest();
        return $this->handleRequest($request, $redirect, $redirectToUrl);
    }

    /**
     * this enables FPRG without controller
     *
     * @param type $request
     */
    public function handleRequest($request, $redirect = null, $redirectToUrl = false)
    {
        if ($request->isPost()) {
            return $this->handlePostRequest($request, $redirect, $redirectToUrl);
        } else {
            return $this->handleGetRequest();
        }
    }

    /**
     * @param  Request       $request
     * @param  string        $redirect      Route or URL string (default: current route)
     * @param  bool          $redirectToUrl Use $redirect as a URL string (default: false)
     * @return Response
     */
    protected function handlePostRequest($request, $redirect, $redirectToUrl)
    {
        $container = $this->getSessionContainer();
        // Run the form validations/filters and retrieve any errors
        $postFiles = $request->getFiles()->toArray();
        $post      = $request->getPost()->toArray();

        if (count($postFiles)) {
            //prepare filter
            $renameFilter = $this->getRenameFilter();
            $thumbnailFilter = $this->getThumbnailFilter();
            $name     = array();
            $newFiles = array();
            $rai = new RecursiveArrayIterator($postFiles);
            $rii = new RecursiveIteratorIterator($rai, RecursiveIteratorIterator::SELF_FIRST);
            $finfo = finfo_open(FILEINFO_MIME_TYPE); // mimetype 拡張モジュール風に mime タイプを返します
            //基本的に、セッション中のファイルを保持することが目的なので、
            //フィルターやバリデーションは、フォーム、フォームハンドラーやコントローラーで実装するべき。
            do {
                if (is_array($rii->current())) {
                    //make nameArray file[foo][bar] to array('file', 'foo', 'bar');
                    $depth = $rii->getDepth();
                    array_splice($name, $depth);
                    $name[$depth] = $rii->key();

                    $file = $rii->current();
                    if ($this->isValidFileArray($file, true)) {
                        //check mimetype and update
                        $mimeType = finfo_file($finfo, $file['tmp_name']);
                        if (strlen($mimeType) && (strtolower($file['type']) !== strtolower($mimeType))) {
                            error_log('unmatched file type ' . $file['type'] . ' to finfo: ' . $mimeType, E_USER_NOTICE);
                            $file['specified_type'] = $file['type'];
                            $file['type'] = $mimeType;
                        }
                        $file = $renameFilter->filter($file);
                        if (isset($thumbnailFilter)) {
                            $file = $thumbnailFilter->filter($file);
                        }
                        //mark this session's uploaded file
                        $fileNameToken = uniqid($file['name']);
                        $file['ns'] = $name;
                        $file['token'] = $fileNameToken;
                        $container->files[$fileNameToken] = $file;
                        //trace and set array('file', 'foo', 'bar') as file[foo][bar] = $file
                        $tracer = &$newFiles;
                        foreach ($name as $index) {
                            if (!isset($tracer[$index])) {
                                $tracer[$index] = array();
                            }
                            $tracer = &$tracer[$index];
                        }
                        $tracer = $file;
                    }
                }
                $rii->next();
            } while($rii->valid());
            finfo_close($finfo);
            $post = array_merge_recursive($post, $newFiles);
        };
        // Save form data in session
        // don't set files expiration hop because files must be kept in session.
        $container->setExpirationHops(1, array('post'));
        $container->post    = $post;

        return $this->redirect($redirect, $redirectToUrl);
    }

    /**
     * @param  Request $request
     * @return bool|array
     */
    protected function handleGetRequest()
    {
        $container = $this->getSessionContainer();
        if (null === $container->post) {
            // No previous post, bail early
            return false;
        }

        // Collect data from session
        $post = $container->post;
        unset($container->post);

        return $post;
    }

    /**
     * @return Container
     */
    public function getSessionContainer()
    {
        if (!isset($this->sessionContainer)) {
            $this->sessionContainer = new Container(strtr(__CLASS__, '\\', '_'));
        }

        if (!isset($this->sessionContainer->files)) {
            $this->sessionContainer->files = array();
        }
        return $this->sessionContainer;
    }

    /**
     * @param  Container $container
     * @return FilePostRedirectGet
     */
    public function setSessionContainer(Container $container)
    {
        $this->sessionContainer = $container;
        return $this;
    }

    /**
     * we can review files in current session
     *
     * array (
     *  'key as tmp_name' => array(
     *      'name' =>
     *      'type' =>
     *      'tmp_name' =>
     *      'error' =>
     *      'size' =>
     *      'ns' =>  //form element name path
     *  ),
     * );
     * @return array
     */
    public function getFiles()
    {
        return $this->getSessionContainer()->files;
    }

    public function hasFile($tmpName)
    {
        $files = $this->getFiles();
        if (is_array($files) && is_string($tmpName)) {
            return isset($files[$tmpName]);
        }
        return false;
    }

    public function getFile($tmpName)
    {
        if ($this->hasFile($tmpName)) {
            return $this->getFiles()[$tmpName];
        }
        return null;
    }

    public function getImageFile($tmpName)
    {
        if ($this->hasFile($tmpName)) {
            $file = $this->getFiles()[$tmpName];
            $path = $file['tmp_name'];
            $info = getimagesize($path);
            if (false === $info || $info[0] === 0 || $info[1] === 0) {
                return null;
            }
            $file['type'] = $info['mime'];
            $file['width'] = $info[0];
            $file['height'] = $info[1];
            return $file;
        }
        return null;
    }

    public function getThumbnailFile($tmpName, $thumbnail)
    {
        if ($this->hasFile($tmpName)) {
            $file = $this->getFiles()[$tmpName];
            if (isset($file['thumbnails'])
                && isset($file['thumbnails'][$thumbnail])) {
                $thumPath = $file['thumbnails'][$thumbnail];
                $thumImageSize = getimagesize($thumPath);
                $thumbnailFile = array(
                    'name' => $file['name'],
                    'tmp_name' => $thumPath,
                    'type' => $thumImageSize['mime'],
                    'width' => $thumImageSize[0],
                    'height' => $thumImageSize[1],
                    'size' => filesize($thumPath),
                    'error' => 0,
                );
                return $thumbnailFile;
            }
        }
        return $this->getImageFile($tmpName);
    }

    /**
     * TODO: Good candidate for traits method in PHP 5.4 with PostRedirectGet plugin
     *
     * @param  string  $redirect
     * @param  bool    $redirectToUrl
     * @return Response
     * @throws \Zend\Mvc\Exception\RuntimeException
     */
    protected function redirect($redirect, $redirectToUrl)
    {
        $controller         = $this->getController();
        $params             = array();
        $options            = array();
        $reuseMatchedParams = false;

        if (null === $redirect) {
            $routeMatch = $controller->getEvent()->getRouteMatch();

            $redirect = $routeMatch->getMatchedRouteName();
            //null indicates to redirect for self.
            $reuseMatchedParams = true;
        }

        if (method_exists($controller, 'getPluginManager')) {
            // get the redirect plugin from the plugin manager
            $redirector = $controller->getPluginManager()->get('Redirect');
        } else {
            /*
             * If the user wants to redirect to a route, the redirector has to come
             * from the plugin manager -- otherwise no router will be injected
             */
            if ($redirectToUrl === false) {
                throw new RuntimeException('Could not redirect to a route without a router');
            }

            $redirector = new Redirect();
        }

        if ($redirectToUrl === false) {
            $response = $redirector->toRoute($redirect, $params, $options, $reuseMatchedParams);
            $response->setStatusCode(303);
            return $response;
        }

        $response = $redirector->toUrl($redirect);
        $response->setStatusCode(303);

        return $response;
    }

    public function setPluginTmpDir($tmpDir = null)
    {
        if (null === $tmpDir) {
            $policy = $this->getTmpDirPolicy();
            $basedir = $this->getTmpBaseDir();
            switch ($policy) {
                default:
                case 'daily':
                    $tmpDir = $basedir . date('d');
                    if (!is_dir($tmpDir)) {
                        mkdir($tmpDir);
                    }
                    $lastDir = $basedir . date('d', time() - 60 * 60 * 24 * 2);
                    $flags = GlobIterator::SKIP_DOTS | GlobIterator::CURRENT_AS_PATHNAME;
                    //
                    $clearFolder = null;//for referrence
                    $clearFolder = function ($dir) use (& $clearFolder, $flags) {
                        $it = new GlobIterator($dir . DIRECTORY_SEPARATOR . '*', $flags);
                        foreach ($it as $pathname) {
                            if ($it->isDir()) {
                                $clearFolder($pathname);
                                rmdir($pathname);
                            } else {
                                unlink($pathname);
                            }
                        }
                    };

                    ErrorHandler::start();
                    $clearFolder($lastDir);
                    $error = ErrorHandler::stop();
                    if ($error) {
                        throw new \RuntimeException("Flushing directory '{$lastDir}' failed", 0, $error);
                    }
                    break;
                case 'hourly':

                    break;
                case 'raw':
                    break;
            }

        }
        $this->tmpDir = $tmpDir;
        $renameFilter = $this->getRenameFilter();
        $renameFilter->setTarget($tmpDir);
        return $this;
    }

    public function getPluginTmpDir()
    {
        if (!isset($this->tmpDir)) {
            $this->setPluginTmpDir();
        }
        return $this->tmpDir;
    }

    public function setTmpBaseDir($tmpBaseDir)
    {
        $this->tmpBaseDir = rtrim($tmpBaseDir, '/\\') . DIRECTORY_SEPARATOR;
    }

    public function getTmpBaseDir()
    {
        return $this->tmpBaseDir;
    }

    public function setTmpDirPolicy($policy)
    {
        $this->tmpDirPolicy = $tmpDirPolicy;
    }

    public function getTmpDirPolicy()
    {
        return $this->tmpDirPolicy;
    }

    public function setRenameFilter(RenameUpload $renameFilter = null)
    {
        if (null === $renameFilter) {
            $options = $this->defaultRenameFilterOptions;
            $renameFilter = new RenameUpload($options);
        }

        if (! $target = $this->getPluginTmpDir()) {
            if (!$target = $renameFilter->getTarget()) {
                $target = ini_get('upload_tmp_dir') ?: sys_get_temp_dir();
            }
            $this->setPluginTmpDir($target);
        }

        $renameFilter->setTarget($target);
        $this->renameFilter = $renameFilter;
        return $this;
    }

    public function getRenameFilter()
    {
        if (!isset($this->renameFilter)) {
            $this->setRenameFilter();
        }
        return $this->renameFilter;
    }

    /**
     * ここまでやるなら、InputFilterにした方がキレイだよねｗ
     *
     * @param \Zend\Filter\FilterInterface $thumbnailFilter
     */
    public function setThumbnailFilter(FilterInterface $thumbnailFilter)
    {
        $this->thumbnailFilter = $thumbnailFilter;
    }

    public function getThumbnailFilter()
    {
        return $this->thumbnailFilter;
    }

    public function setUploadFileValidator(UploadFile $uploadFileValidator = null)
    {
        if (null === $uploadFileValidator) {
            $this->uploadFileValidator = new UploadFile;
        } else {
            $this->uploadFileValidator = $uploadFileValidator;
        }
        return $this;
    }

    public function getUploadFileValidator()
    {
        if (!isset($this->uploadFileValidator)) {
            $this->setUploadFileValidator();
        }
        return $this->uploadFileValidator;
    }

    public function getMessages()
    {
        return $this->errorMessages;
    }

    public function isValid($value)
    {
        // only has keystring
        if (is_string($value)) {
            if ($this->hasFile($value)) {
                return true;
            } else {
                $this->errorMessages[$value] = $value . ' is not found';
                return false;
            }
        } elseif (is_array($value)) {
            if (! ArrayUtils::isList($value)) {
                $value = array($value);
            }
            foreach ($value as $file) {
                if (!$this->isValidFileArray($file, false, true)) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }

    /**
     * チェック対象は定数化する？
     *
     * @param type $fileArray
     * @param type $uploadedCheck
     * @param type $tokenCheck
     * @return boolean
     */
    protected function isValidFileArray($fileArray, $uploadedCheck = false, $tokenCheck = false)
    {
        if (!is_array($fileArray)) {
            return false;
        }

        $arrayCheck = isset($fileArray['name'])
                    && is_string($fileArray['name'])
                    && isset($fileArray['type'])
                    && is_string($fileArray['type'])
                    && isset($fileArray['tmp_name'])
                    && is_string($fileArray['tmp_name'])
                    && isset($fileArray['error'])
                    && is_int($fileArray['error'])
                    && isset($fileArray['size'])
                    && is_int($fileArray['size']);

        if (!$arrayCheck) {
            return false;
        }

        if ($uploadedCheck) {
            if (! $this->getUploadFileValidator()->isValid($fileArray)) {
                return false;
            }
        }

        if ($tokenCheck) {
            if (!isset($fileArray['token'])
                || !is_string($fileArray['token'])
                || !$this->hasFile($fileArray['token'])) {
                return false;
            }
        }

        return true;
    }

    public function setRequestKey($requestKey)
    {
        $this->requestKey = (string) $requestKey;
    }

    public function getRequestKey()
    {
        return $this->requestKey;
    }

    /**
     *
     *
     * @param string|null $route
     * @param array $params
     */
    public function setPreviewRoute($route, $params = array())
    {
        $this->previewRoute = $route;
        if (count($params)) {
            $this->previewParams = $params;
        }
    }

    public function getPreviewRoute()
    {
        return $this->previewRoute;
    }

    public function setPreviewParams(array $params)
    {
        $this->previewParams = $params;
    }

    public function getPreviewParams()
    {
        return $this->previewParams;
    }
}
