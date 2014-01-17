<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace FlowerTest\Model\Service\TestAsset;

use Flower\Model\RepositoryInterface;
/**
 * Description of CocreteRepository
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ConcreteRepository implements RepositoryInterface {

    public function initialize()
    {
        
    }

    public function isInitialized()
    {
        return true;
    }

}
