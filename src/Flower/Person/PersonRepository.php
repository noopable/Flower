<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Person;

use Flower\Person\Exception\DomainException;
use Flower\Hash\Hash1;
use Flower\Model\AbstractDbTableRepository;
use Flower\Person\Identical\EmailInterface;
use Zend\Validator\EmailAddress as EmailAddressValidator;
use Zend\Db\Adapter\Exception\InvalidQueryException as ZendInvalidQueryException;

/**
 * Description of PersonRepository
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class PersonRepository extends AbstractDbTableRepository {

    protected $minimumPasswordLength = 6;

    protected $defaultPasswordLength = 8;

    protected $emailRepository;

    /**
     *
     * @param string $mailaddress
     * @param string $password
     * @param int $domainId
     * @return PersonInterface
     * @throws \Exception
     * @throws \Flower\Person\Exception\DomainException
     */
    public function createPerson($name, $mailaddress, $password = null, $domainId = 0)
    {
        $person = $this->create();
        $person->domain_id = (int) $domainId;
        $emailValidator = new EmailAddressValidator;
        if (! $emailValidator->isValid($mailaddress)) {
            throw new DomainException(implode("\n", $emailValidator->getMessages()), DomainException::INVALID_EMAIL);
        }
        $emailRepository = $this->getEmailRepository();
        $person->name = (string) $name;
        $person->email = $mailaddress;
        try {
            $this->beginTransaction();
            $this->save($person, true);
            $personId = $this->dao->lastInsertValue;
            //valud object -> entity
            $person->setPersonId($personId);
            $email = $emailRepository->create();
            $email->name = $person->name;
            if (isset($password)) {
                if (strlen($password) < $this->minimumPasswordLength) {
                    //サービス仕様としてはサービスレイヤーで設定すること。
                    //6文字以上の制限は良心的配慮。不要なら修正してください。
                    throw new DomainException('password length is too short.', DomainException::PASSWORD_TOO_SHORT);
                }
                $email->password = $password;
            } else {
                $email->password = Hash1::createNewPassword($this->defaultPasswordLength);
            }
            $email->setIdentity($mailaddress);
            $email->setPersonId($personId);
            //auto generate. If you want to customize it, you can overwrite it or save yours at after.
            //http://www.php.net/manual/en/function.crc32.php
            //do you sprintf ?
            $email->addActivationCode(substr(crc32(Hash1::createNewPassword(10)), 1, 8));
            $emailRepository->save($email, true);
            $person->addEmail($email);// its inner code: $email->setPersonId
            $this->commit();
        } catch (ZendInvalidQueryException $ex) {
            $this->rollback();
            $message = $ex->getMessage();
            if (preg_match('/(Duplicate entry)/i', $message)) {
                throw new DomainException('ご指定のメールアドレスは既に使用されている可能性があります。', DomainException::DUPLICATE_ENTRY, $ex);
            }
            throw $ex;
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
        $emailRepository->initialize();
        $this->emailRepository = $emailRepository;
    }

    public function getEmailRepository()
    {
        return $this->emailRepository;
    }
}
