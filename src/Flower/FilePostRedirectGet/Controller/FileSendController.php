<?php

namespace Flower\FilePostRedirectGet\Controller;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\Response\Stream;

class FileSendController extends AbstractActionController
{
    public function imageAction()
    {
        $fprg = $this->fprg();
        $token = $this->getFPRGToken();
        $thumbnail = $this->params()->fromRoute('thumbnail', false);
        $response = $this->getEvent()->getResponse();

        if (!$token) {
            $response->setStatusCode(404);
            return $response;
        }

        if (! $fprg->isValid($token)) {
            $response->setStatusCode(403);
            return $response;
        }

        if ($thumbnail) {
            $file = $fprg->getThumbnailFile($token, $thumbnail);
        } else {
            $file = $fprg->getImageFile($token);
        }

        //$file is safe as to $fprg has files uploaded only in the session.
        return $this->sendFile($file);
    }

    /**
     * FPRGで、ファイルを送出するケースは考えにくいが・・・FLASHとか？
     *
     * @return type
     */
    public function fileAction()
    {
        $fprg = $this->fprg();
        $token = $this->getFPRGToken();
        $response = $this->getEvent()->getResponse();

        if (!$token) {
            $response->setStatusCode(404);
            return $response;
        }

        if (! $fprg->isValid($token)) {
            $response->setStatusCode(403);
            return $response;
        }

        $file = $fprg->getFile($token);
        //$file is safe as to $fprg has files uploaded only in the session.
        return $this->sendFile($file);
    }

    protected function getFPRGToken()
    {
        $fprg = $this->fprg();
        $requestKey = $fprg->getRequestKey();
        return $this->params()->fromRoute($requestKey, false);
    }

    protected function sendFile($file)
    {
        $response = $this->getEvent()->getResponse();
        $path = $file['tmp_name'];
        if (!is_file($path)) {
            $response->setStatusCode(404);
            return $response;
        }

        if (!$response instanceof Stream) {
            $response = new Stream;
            $this->getEvent()->setResponse($response);
        }
        $stream = fopen('file://' . $path, 'r');
        $headers = $response->getHeaders();
        $headers->addHeaderLine('content-type', $file['type']);
        $headers->addHeaderLine('content-transfer-encoding', 'BINARY');
        $response->setContentLength($file['size']);
        $response->setStream($stream);
        $response->setStreamName($file['name']);
        return $response;

    }

    public function csvAction()
    {
        //CSVのプレビュー？
    }
}