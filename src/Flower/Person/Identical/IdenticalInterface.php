<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Person\Identical;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface IdenticalInterface {
    public function getPersonId();
    public function setPersonId($personId);
    public function setRoles($roles);
    public function getRoles();
    public function setIdentity($identity);
    public function getIdentity();
    public function setPlainCredential($plainCredential);
    public function setCredential($credential);
    public function getCredential();
    public function newCredential();
}
