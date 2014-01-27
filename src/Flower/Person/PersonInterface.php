<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Person;

use Flower\Person\Identical\EmailInterface;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface PersonInterface {
    public function getPersonId();

    public function setPersonId($personId);

    public function addEmail(EmailInterface $email);

    public function getEmails();

    public function removeEmail(EmailInterface $email);
}
