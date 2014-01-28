<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace FlowerTest\Model\TestAsset;

use Flower\Model\AbstractEntity;
/**
 * Description of ConcreteEntity
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ConcreteEntity extends AbstractEntity{

    public $prop1 = 'prop1Value';

    protected $prop2 = 'prop2Value';

    public function getIdentifier()
    {
        return array('entity_id');
    }
}
