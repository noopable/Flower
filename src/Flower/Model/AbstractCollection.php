<?php

namespace Flower\Model;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use \ArrayObject;
use Zend\Db\ResultSet\ResultSetInterface as ResultSet;

abstract class AbstractCollection extends ArrayObject
{
    public function setResultSet(ResultSet $resultSet)
    {
        
    }
}