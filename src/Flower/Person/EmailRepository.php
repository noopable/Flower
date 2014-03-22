<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Person;

use Flower\Hash\Hash1;
use Flower\Model\AbstractDbTableRepository;
use Flower\Model\AbstractEntity;

/**
 * Description of EmailRepository
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class EmailRepository extends AbstractDbTableRepository
{
    public function save(AbstractEntity $entity, $forceInsert = false)
    {
        $email = clone $entity;
        if (isset($email->password)) {
            $email->credential = $this->hash($entity->password);
        }
        return parent::save($email, $forceInsert);
    }

    public function hash($credential)
    {
        return Hash1::hash($credential);
    }
}
