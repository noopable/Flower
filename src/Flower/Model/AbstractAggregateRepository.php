<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Model;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Description of AbstractAggregateRepository
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
abstract class AbstractAggregateRepository
 implements RepositoryInterface, ServiceLocatorAwareInterface, RepositoryPluginManagerAwareInterface
{
    //put your code here
    use ServiceLocatorAwareTrait;
    use RepositoryPluginManagerAwareTrait;

    public function initialize()
    {

    }
}
