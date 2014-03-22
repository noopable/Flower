<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Person;

use Flower\Exception\DomainException;
use Flower\Model\AbstractDbTableRepository;
use Flower\Person\Identical\Email;
use Flower\Person\Identical\EmailInterface;
use Zend\Validator\EmailAddress as EmailAddressValidator;

/**
 * Description of PersonRepository
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class PersonRepository extends AbstractDbTableRepository {

    protected $emailRepository;

    public function createPerson($mailaddress, $domainId = 0)
    {
        $person = $this->create();
        $person->domain_id = (int) $domainId;
        $emailValidator = new EmailAddressValidator;
        if (! $emailValidator->isValid($mailaddress)) {
            throw new DomainException(implode("\n", $emailValidator->getMessages()));
        }
        $emailRepository = $this->getEmailRepository();
        $person->email = $mailaddress;
        try {
            $this->beginTransaction();
            $this->save($person, true);
            $personId = $this->dao->lastInsertValue;
            //valud object -> entity
            $person->setPersonId($personId);
            $email = $emailRepository->create();
            $email->setIdentity($mailaddress);
            $email->setPersonId($personId);
            $emailRepository->save($email, true);
            $person->addEmail($email);// its inner code: $email->setPersonId
            $this->commit();
        } catch (\Exception $ex) {
            $this->rollback();
            throw $ex;
        }

        return $person;
    }

    public function getPerson($personId)
    {
        $person = $this->getEntity(array('person_id' => $personId));
        $emails = $this->getEmailRepository()->getCollection(array('primary_person_id' => $personId));
        foreach ($emails as $email) {
            if ($email instanceof EmailInterface) {
                $person->addEmail($email);
            }
        }
        return $person;
    }

    public function savePerson(PersonInterface $person, $forceInsert = false, $emailForceInsert = false)
    {
        $emails = $person->getEmails();
        $emailRepository = $this->getEmailRepository();
        //start transaction start?
        //アダプターからコネクションを取得して開始する。
        try {
            $this->beginTransaction();
            foreach ((array) $emails as $email) {
                if ($email instanceof EmailInterface) {
                    $emailRepository->save($email, $emailForceInsert);
                }
            }
            $res = $this->save($person, $forceInsert);
            $this->commit();
        } catch (\Exception $ex) {
            $this->rollback();
            throw $ex;
        }

        return $res;
    }

    public function setEmailRepository(EmailRepository $emailRepository)
    {
        $this->emailRepository = $emailRepository;
    }

    public function getEmailRepository()
    {
        return $this->emailRepository;
    }
}
