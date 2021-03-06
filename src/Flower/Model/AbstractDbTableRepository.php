<?php
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Model;

use ArrayObject;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\TableGateway\AbstractTableGateway;
use Flower\Model\AbstractEntity;
use Flower\Model\Exception;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Stdlib\ArrayUtils;

abstract class AbstractDbTableRepository extends AbstractRepository
 implements RepositoryInterface, ServiceLocatorAwareInterface, RepositoryPluginManagerAwareInterface
{
    use ServiceLocatorAwareTrait;
    use RepositoryPluginManagerAwareTrait;

    protected $dao;

    protected $mappingMethods;

    protected $entityPrototype;

    protected $select;

    Protected $resourceLocator;
    /**
     *
     * @param $name
     * @param $entityPrototype
     * @param $tableGateway
     */
    public function __construct($name = null, $entityPrototype, TableGatewayInterface $tableGateway)
    {
        if (null !== $name) {
            $this->setName($name);
        }

        $this->entityPrototype = $entityPrototype;

        $this->dao = $tableGateway;

    }

    public function initialize()
    {
        if ($this->isInitialized) {
            return;
        }
        $this->isInitialized = true;

        /*
        $sl = $this->getServiceLocator();
        if (!isset($sl)) {
            throw new Exception\RuntimeException(__CLASS__ . ' depends on the PluginManager, but not given');
        }
         *
         */

        if ($this->dao instanceof AbstractTableGateway) {
            $this->dao->initialize();
            $this->adapter = $this->dao->getAdapter();
        }

        if (!isset($this->adapter)) {
            throw new \RuntimeException('todo: fix exception: missing adapter');
        }

        if (($prototype = $this->getEntityPrototype())
                && ($prototype instanceof ArrayObject || method_exists($prototype, 'getArrayCopy'))) {
            $this->dao->getResultSetPrototype()
                ->setArrayObjectPrototype($prototype);
        }

        if (!isset($this->select)) {
            //get from abstract factory;
            $this->select = $this->getTableGateway()->getSql()->select();
        }

        if ($columns = $this->getOption('columns', false)) {
            /**
             * @see http://framework.zend.com/manual/2.0/en/modules/zend.db.sql.html#columns
             * // as array of names
                    $select->columns(array('foo', 'bar'));

                    // as an associative array with aliases as the keys:
                    // produces 'bar' AS 'foo', 'bax' AS 'baz'

                    $select->columns(array('foo' => 'bar', 'baz' => 'bax'));
             */
            $this->select->columns($columns);
        }

        if ($selectOptions = $this->getOption('select', false)) {
            if (isset($selectOptions['join'])) {
                $joins = $selectOptions['join'];
                if (is_array($joins)) {
                    if (ArrayUtils::isHashTable($joins)) {
                        $joins = array($joins);
                    }
                    foreach ($joins as $join) {
                        if (isset($join['name']) && isset($join['on'])) {
                            $name = $join['name'];
                            $on   = $join['on'];
                            $columns = isset($join['columns']) ? $join['columns'] : Sql\Select::SQL_STAR;
                            $type    = isset($join['type']) ?  $join['type'] : Sql\Select::JOIN_INNER;
                            $this->select->join($name, $on, $columns, $type);
                        }
                    }
                }
            }

            if (isset($selectOptions['having'])) {
                if (is_array($selectOptions['having'])) {
                    $having = array_shift($selectOptions['having']);
                    if (count($selectOptions['having']) > 0) {
                        $combination = array_shift($selectOptions['having']);
                    }
                }
                else {
                    $having = $selectOptions['having'];
                }

                if (isset($combination)) {
                    $this->select->having($having, $combination);
                }
                else {
                    $this->select->having($having);
                }
            }

            $optionKeys = array('group', 'where', 'limit', 'offset', 'order');
            foreach ($optionKeys as $key) {
                if (isset($selectOptions[$key])) {
                    $this->select->$key($selectOptions[$key]);
                }
            }

            if (isset($selectOptions['closure'])) {
                $selectOptions['closure']($this->select);
            }
        }
    }

    public function setAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function getAdapter()
    {
        return $this->adapter;
    }

    public function getEntityPrototype()
    {
        return $this->entityPrototype;
    }

    public function setTableGateway($tableGateway)
    {
        $this->dao = $tableGateway;
    }

    public function getTableGateway()
    {
        return $this->dao;
    }

    public function getSelect($clone = true)
    {
        if (!isset($this->select)) {
            //get from abstract factory;
            $this->select = $this->getTableGateway()->getSql()->select();
        }
        
        if ($clone) {
            return clone $this->select;
        } else {
            return $this->select;
        }
    }

    public function getEntity($where = null)
    {
        $select = $this->getSelect();
        if (null !== $where) {
            $select->where($where);
        }
        $select->limit(1);
        $resultSet = $this->dao->selectWith($select);
        return $resultSet->current();
    }

    public function getCollection($where = null, $limit = null)
    {
        $select = $this->getSelect();
        if (null !== $where) {
            $select->where($where);
        }

        if (null !== $limit) {
            $select->limit($limit);
        }

        return $this->dao->selectWith($select);
    }

    public function getCollectionWithStrategy(SelectStrategyInterface $strategy)
    {
        $select = $this->getSelect();// has cloned
        $strategy->select($select);
        return $this->dao->selectWith($select);
    }

    public function create()
    {
        $prototype = $this->getEntityPrototype();
        return clone $prototype;
    }

    public function save(AbstractEntity $entity, $forceInsert = false)
    {
        //entityは常にIDを持つ。だからIDをベースにした更新で問題ない。
        //他の要素によるupdateは別途直接サービスメソッドからビルドしてください。
        $identifier = $entity->getIdentifier();
        $data = $entity->getArrayCopy();

        if ($columns = $this->getOption('columns', false)) {
            //$setは、実テーブルのカラム名をキーとした配列になる
            $set = array_flip($columns);
            //identifierは、変換後テーブルの主キーなので、それをキー配列から取り出す
            //変換後キー（エンティティカラム）をキーとし、返還前実カラム名を値とする主キー配列
            $identifier = array_flip(array_intersect($set, $identifier));

            $w = array();
            foreach ($set as $key => $value) {
                //エンティティに指定データが存在すれば、セットする
                //なければ、ダミーデータを削除する。
                if (isset($data[$value])) {
                    $set[$key] = $data[$value];
                }
                else {
                    unset($set[$key]);
                }
                //カラム変換後のフィールドが主キー配列のキーに存在すれば
                //条件節のキー名は実カラム名、値は実データ
                if (isset($identifier[$value]) && isset($data[$value])) {
                    $w[$key] = $data[$value];
                }
            }
        }
        else {
            $w   = array_intersect_key($data, array_flip($identifier));
            $set = array_diff_key($data, $w);
        }

        if (count($identifier) === count($w) && !$forceInsert) {
            $res = $this->dao->select($w);
            if ($res->count()) {
                //use REPLACE INTO ?
                if (count($set) === 0) {
                    throw new Exception\InvalidArgumentException('update? no update columns');
                }
                return $this->dao->update($set, $w);
            }
        }

        $aInsert = $set + $w;
        if (count($aInsert) === 0) {
            $ei = new Exception\InvalidArgumentException('insert detected but no columns');
            $er = new Exception\RuntimeException('Invalid configuration of columns? ', 0, $ei);
            throw $er;
        }
        return $this->dao->insert($aInsert);

    }


    public function delete(AbstractEntity $entity)
    {
        $identifier = $entity->getIdentifier();
        $data = $entity->getArrayCopy();

        $where = array_intersect_key($data, array_flip($identifier));
        if ($columns = $this->getOption('columns', false)) {
            $tmpWhere = array();
            foreach ($where as $key => $value) {
                $tmpWhere[$columns[$key]] = $value;
            }
            $where = $tmpWhere;
        }
        //$this->dao->delete($where);
    }

    public function setMappingMethods(array $methods)
    {
        $this->mappingMethods = $methods;
    }

    public function __call($name, $params = null)
    {
        if (isset($this->mappingMethods[$name])
            && is_callable($this->mappingMethods[$name])) {
            if (null === $params) {
                return call_user_func($this->mappingMethods[$name], $this->dao);
            }
            else {
                array_unshift($params, $this->dao);
                return call_user_func_array($this->mappingMethods[$name], $params);
            }
        }
    }

    public function beginTransaction()
    {
        return $this->getAdapter()->getDriver()->getConnection()->beginTransaction();
    }

    public function commit()
    {
        return $this->getAdapter()->getDriver()->getConnection()->commit();
    }

    public function rollback()
    {
        return $this->getAdapter()->getDriver()->getConnection()->rollback();
    }
}