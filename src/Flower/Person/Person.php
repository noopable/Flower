<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Person;

use Flower\Exception\DomainException;
use Flower\Model\AbstractEntity;
use Flower\Person\Identical\EmailInterface;
/**
 * Description of Person
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class Person extends AbstractEntity implements PersonInterface {

    protected $identifier = array('person_id');

    protected $emails;

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getPersonId()
    {
        if (!isset($this->person_id)) {
            throw new DomainException('person_id is required');
        }
        return $this->person_id;
    }

    public function setPersonId($personId)
    {
        $this->person_id = $personId;
    }

    public function addEmail(EmailInterface $email)
    {
        $email->setPersonId($this->getPersonId());
        $this->emails[$email->getIdentity()] = $email;
    }

    public function getEmails()
    {
        return $this->emails;
    }

    public function removeEmail(EmailInterface $email)
    {
        $identity = $email->getIdentity();
        if (isset($this->emails[$identity])) {
            unset($this->emails[$identity]);
        }
    }

}
