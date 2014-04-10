<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Model\Strategy;

use Flower\Model\SelectStrategyInterface;
use Zend\Db\Sql\Select;
use Zend\Stdlib\ArrayUtils;

/**
 * Description of DigestStrategy
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class SelectOptionsStrategy implements SelectStrategyInterface
{
    protected $options = array();

    public function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    public function select(Select $select)
    {
        if (isset($this->options['columns'])) {
            $select->columns($this->options['columns']);
        }

        if (isset($this->options['join'])) {
            $joins = $this->options['join'];
            if (is_array($joins)) {
                if (ArrayUtils::isHashTable($joins)) {
                    $joins = array($joins);
                }
                foreach ($joins as $join) {
                    if (isset($join['name']) && isset($join['on'])) {
                        $name = $join['name'];
                        $on   = $join['on'];
                        $columns = isset($join['columns']) ? $join['columns'] : Select::SQL_STAR;
                        $type    = isset($join['type']) ?  $join['type'] : Select::JOIN_INNER;
                        $select->join($name, $on, $columns, $type);
                    }
                }
            }
        }

        if (isset($this->options['having'])) {
            $having = $this->options['having'];
            if (is_array($having)) {
                $having = array_shift($having);
                if (count($having) > 0) {
                    $combination = array_shift($having);
                }
            }

            if (isset($combination)) {
                $select->having($having, $combination);
            } else {
                $select->having($having);
            }
        }

        $optionKeys = array('group', 'where', 'limit', 'offset', 'order');
        foreach ($optionKeys as $key) {
            if (isset($this->options[$key])) {
                $select->$key($this->options[$key]);
            }
        }
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions(array $options = array(), $merge = true)
    {
        if ($merge) {
            $this->options = ArrayUtils::merge($this->options, $options);
        } else {
            $this->options = $options;
        }
    }

    public function setColumns($columns)
    {
        $this->options['columns'] = $columns;
    }

    public function setJoin($join)
    {
        $this->options['join'] = $join;
    }

    public function setHaving($having)
    {
        $this->options['having'] = $having;
    }

    public function setLimit($limit)
    {
        $this->options['limit'] = $limit;
    }

    public function setOffset($offset)
    {
        $this->options['offset'] = $offset;
    }

    public function setGroup($group)
    {
        $this->options['group'] = $group;
    }

    public function setOrder($order)
    {
        $this->options['order'] = $order;
    }

    public function setWhere($where)
    {
        $this->options['where'] = $where;
    }
}

