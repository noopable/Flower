<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Person;

use Flower\Model\AbstractDbTableRepository;
use Flower\Person\Identical\EmailInterface;

/**
 * Description of PersonRepository
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class PersonRepository extends AbstractDbTableRepository {

    protected $emailRepository;

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

    public function savePerson(PersonInterface $person)
    {
        $emails = $person->getEmails();
        $emailRepository = $this->getEmailRepository();
        //start transaction start?
        //アダプターからコネクションを取得して開始する。
        try {
            $this->beginTransaction();
            foreach ($emails as $email) {
                if ($email instanceof EmailInterface) {
                    $emailRepository->save($email);
                }
            }
            $res = $this->save($person);
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
