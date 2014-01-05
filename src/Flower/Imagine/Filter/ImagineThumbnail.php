<?php
namespace Flower\Imagine\Filter;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

use Zend\Filter\FilterInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Box;
/**
 * Description of Image
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ImagineThumbnail implements FilterInterface
{

    protected $imagine;

    protected $strategy;

    protected $thumbnails;

    protected $defaultThumbDir = '__thumbnails';

    protected $defaultMode = ImageInterface::THUMBNAIL_INSET;

    protected $defaultMaxSize = 786432;// 1024 * 768

    protected $extensions = array(
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'wbmp' => 'image/vnd.wap.wbmp',
            'png' => 'image/png',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
        );

    public function filter($value)
    {
        if (!isset($value['tmp_name'])) {
            return $value;
        }

        $this->resizeToMaxSize($value);

        $thumbnails = $this->getThumbnails();

        if (! count($thumbnails)) {
            return $value;
        }

        $imagine = $this->getImagine();

        if (!$imagine) {
            return $value;
        }

        $type = $value['type'];
        $extension = array_search($type, $this->extensions);
        if (!$extension) {
            $imageSize = getimagesize($value['tmp_name']);
            if ($type !== $imageSize['mime']) {
                $type = $imageSize['mime'];
                $extension = array_search($type, $this->extensions);
            }
            if (!$extension) {
               throw new \RuntimeException('cannot detect extension for type:' . $type . ' ( is it image ?)');
            }
        }
        $extension = '.' . $extension;
        $fileName = basename($value['tmp_name'], $extension);
        $dir = dirname(realpath($value['tmp_name']));
        if (! is_dir($dir)) {
            throw new \RuntimeException('specified file is not file or attack?');
        }
        $perms = fileperms($dir);

        $strategy = $this->getThumbnailStrategy();

        if (isset($strategy['default-mode'])) {
            $defaultMode = $strategy['default-mode'];
        } else {
            $defaultMode = $this->defaultMode;
        }

        if (isset($strategy['use-thumb-dir'])) {
            if (is_bool($strategy['use-thumb-dir']) && $strategy['use-thumb-dir']) {
                $thumbDir = $this->defaultThumbDir;
            } elseif (is_string($strategy['use-thumb-dir'])) {
                $thumbDir = (string) $strategy['use-thumb-dir'];
            }
            if (isset($thumbDir)) {
                $dir = $dir . DIRECTORY_SEPARATOR . $thumbDir;
                if (!is_dir($dir)) {
                    mkdir($dir, $perms, true);
                }
            }
        }


        if (!isset($value['thumbnails'])) {
            $value['thumbnails'] = array();
        }
        foreach ($thumbnails as $name => $thumbnail) {
            $name = strtr($name, '.\/', '_');
            switch (count($thumbnail)) {
                case 1:
                    return $value;
                case 2:
                    $thumbnail[] = $defaultMode;
                    break;
            }
            list($width, $height, $mode) = $thumbnail;

            $tmp = $dir . DIRECTORY_SEPARATOR . $name;
            if (!is_dir($tmp)) {
                mkdir($tmp, $perms, true);
            }
            $newFilename = $tmp . DIRECTORY_SEPARATOR . $fileName . $extension;
            $image = $imagine->open($value['tmp_name']);
            $box = new Box($width, $height);
            $image->thumbnail($box, $mode)->save($newFilename);
            //結果チェック？
            $value['thumbnails'][$name] = $newFilename;
        }
        return $value;
    }

    public function setImagine(ImagineInterface $imagine)
    {
        $this->imagine = $imagine;
    }

    public function getImagine()
    {
        return $this->imagine;
    }

    public function setThumbnailStrategy(array $strategy)
    {
        $this->strategy = $strategy;
    }

    public function getThumbnailStrategy()
    {
        return $this->strategy;
    }

    public function setThumbnails(array $thumbnails)
    {
        $this->thumbnails = $thumbnails;
    }

    public function getThumbnails()
    {
        return $this->thumbnails;
    }

    protected function resizeToMaxSize(&$file)
    {
        $this->imageSize($file);
        $width = $file['width'];
        $height = $file['height'];
        $math = $width * $height;
        $strategy = $this->getThumbnailStrategy();
        if (isset($strategy['max-size'])) {
            $maxSize = $strategy['max-size'];
        } else {
            $maxSize = $this->defaultMaxSize;
        }

        if ($maxSize > $math) {
            return $file;
        }
        $recio = sqrt($maxSize / $math);
        $newWidth = intval($width * $recio);
        $newHeight = intval($height * $recio);
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        if (! $newImage) {
            throw new \RuntimeException('failed to make new image width:' . $newWidth . ':height:' . $newHeight);
        }
        $type = $file['type'];
        switch ($type) {
            case "image/jpeg":
                $source = imagecreatefromjpeg($file['tmp_name']);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($file['tmp_name']);
                break;
            case 'image/png':
                $source = imagecreatefrompng($file['tmp_name']);
                break;
            case 'image/vnd.wap.wbmp':
                $source = imagecreatefromwbmp($file['tmp_name']);
                break;
            default:
                $source = imagecreatefromstring(file_get_contents($file['tmp_name']));
                break;
        }

        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        switch ($type) {
            case "image/jpeg":
                imagejpeg($newImage, $file['tmp_name']);
                break;
            case 'image/gif':
                imagegif($newImage, $file['tmp_name']);
                break;
            case 'image/png':
                imagepng($newImage, $file['tmp_name']);
                break;
            case 'image/vnd.wap.wbmp':
                imagewbmp($newImage, $file['tmp_name']);
                break;
            default:
                $file['type'] = 'image/jpeg';
                imagejpeg($newImage, $file['tmp_name']);
                break;
        }

        imagedestroy($source);
        imagedestroy($newImage);
        return $file;
    }

    protected function imageSize(&$file)
    {
        if (isset($file['width']) && isset($file['height']) && isset($file['type'])) {
            return $file;
        }
        $imagesize = getimagesize($file['tmp_name']);
        $file['width'] = $imagesize[0];
        $file['height'] = $imagesize[1];
        $file['type'] = $imagesize['mime'];
        return $file;
    }
}
