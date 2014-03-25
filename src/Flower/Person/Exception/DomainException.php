<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Person\Exception;

/**
 * Description of DomainException
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class DomainException extends \DomainException implements ExceptionInterface {
    const DUPLICATE_ENTRY = 0x01;
    const PASSWORD_TOO_SHORT = 0x02;
}
