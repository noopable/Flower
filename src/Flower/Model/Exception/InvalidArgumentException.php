<?php
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\Model\Exception;

use Flower\Exception;
use Zend\Db\Exception\InvalidArgumentException as ZFIAE;

class InvalidArgumentException extends ZFIAE implements Exception\ExceptionInterface
{
}
