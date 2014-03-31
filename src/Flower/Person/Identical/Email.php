<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Person\Identical;

use Flower\Exception\DomainException;
use Flower\Hash\Hash1;
use Flower\Model\AbstractEntity;
/**
 * Description of Email
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class Email extends AbstractEntity implements EmailInterface {

    protected $identifier = array('email');

    protected $maskFields = array('credential', 'activation_code');

    public function __toString()
    {
        return $this->email;
    }

    public function getCredential()
    {
        return $this->credential;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getIdentity()
    {
        return $this->email;
    }

    public function getPersonId()
    {
        return $this->person_id;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setCredential($credential)
    {
        $this->credential = $credential;
    }

    public function setIdentity($identity)
    {
        $this->email = $identity;
    }

    public function setPersonId($personId)
    {
        $this->person_id = $personId;
    }

    public function newCredential()
    {
        $password = Hash1::createNewPassword();
        $this->setPlainCredential($password);
        return $password;
    }

    public function setPlainCredential($plainCredential)
    {
        $this->credential = Hash1::hash($plainCredential);
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    public function addActivationCode($code, $time = null)
    {
        if (null === $time) {
            $time = time();
        }
        if (isset($this->activation_code) && strlen($this->activation_code)) {
            $code = $this->activation_code . "\n" . $code;
        }

        $this->activation_code = $code . "/" . $time;
        return $this;
    }
}
