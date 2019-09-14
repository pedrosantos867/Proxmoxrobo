<?php
namespace System;


use PDO;
use stdClass;
use System\Db\Db;


abstract class ObjectModel
{

    protected static $instance;

    public $id;
    public $validateErrors = array();

    protected static $table;

    protected static $translated = array();

    public $removedObject;
    public $object;

    public $db;

    private $_queryOptions = array('where' => array());
    private $_lastQueryOptions = array();

    protected static $instances;

    protected $_sortable = false;
    private $_enableRemoveSafe = false;
    private $_enableRemovedRows = 0;

    private $debugQuery = '';
    private $debugInfo = array('start' => 0, 'end'=>0);

    public static function getInstance()
    {
        $class = get_called_class();

        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class;
        }

        return self::$instances[$class];
    }

    public static function factory()
    {
        $class = get_called_class();

        if (!isset(self::$instances[$class])) {

            if (func_num_args() > 0) {

                $arg = func_get_arg(0);

            } else {
                $arg = null;
            }
            self::$instances[$class] = new $class($arg);
        }

        return self::$instances[$class];
    }


    public function __construct($id = null, $id_lang = 0, $init = true)
    {
        //   global $lang;

        if (!static::$table) {
            new Exception('$table не определенна');
        }
        if ($id) {
            if (is_object($id)) {
                $this->object = $id;
                $this->id     = $this->object->id;
            } else {

                $this->id = $id;
                if (!empty(static::$translated) && $id_lang) {
                    $select = array();
                    foreach (static::$translated as $field) {
                        $select[] = "`" . _DB_PREFIX_ . static::$table . "`.`" . $field . "` as `" . $field . "_0`, `" . _DB_PREFIX_ . static::$table . "_lang`.`" . $field . "` as `" . $field . "_" . $id_lang . "`";
                    }
                    $res = Db::getInstance()->prepare("
                        SELECT `" . _DB_PREFIX_ . static::$table . "`.* ,  " . implode(',', $select) . "
                        FROM `" . _DB_PREFIX_ . $this::$table . "`
                        LEFT JOIN `" . _DB_PREFIX_ . $this::$table . "_lang` ON `" . _DB_PREFIX_ . $this::$table . "`.`id` = `" . _DB_PREFIX_ . $this::$table . "_lang`.`id_" . \Tools::depluralize($this::$table) . "`
                        WHERE `" . _DB_PREFIX_ . $this::$table . "`.`id` = :ID AND  `" . _DB_PREFIX_ . $this::$table . "_lang`.`id_lang` = " . $id_lang . "
                    ");
                    $res->bindValue("ID", $this->id, PDO::PARAM_INT);
                    $res->execute();
                    $result = $res->fetchObject();
                    foreach (static::$translated as $field) {
                        if (isset($result->{$field . '_' . $id_lang}) && $result->{$field . '_' . $id_lang} != '') {
                            $result->$field = $result->{$field . '_' . $id_lang};
                        }
                    }
                    $this->object = $result;
                }
                if (empty(static::$translated) || empty($this->object)) {
                    $res = Db::getInstance()->prepare('SELECT * FROM `' . _DB_PREFIX_ . $this::$table . '`  WHERE id = :ID');
                    $res->bindValue("ID", $this->id, PDO::PARAM_INT);

                    $res->execute();

                    $this->object = $res->fetchObject();
                }

                if (!isset($this->object->id)) {
                    return false;
                }
            }
            if ($init) {
                $this->_init();
            }
        } else {
            $this->object = new stdClass();
        }
    }


    public function debug()
    {
        $start = $this->debugInfo['start'];
        $end = $this->debugInfo['end'];

        echo "This query took " . ($end - $start) . " seconds.";
        echo '<pre>';
        echo $this->debugQuery;
        echo '</pre>';

    }

    public function db()
    {
        if (!is_object($this->db)) {
            $this->db = Db::getInstance();
        }

        return $this->db;
    }


    public static function load($id)
    {
        return self::__construct($id);
    }


    public function isTranslated()
    {
        if (empty($this::$translated)) {
            return false;
        } else {
            return true;
        }
    }

    public function multiLoad($array_ids)
    {
        $array_ids = array_unique($array_ids);
        foreach ($array_ids as $id) {
            $this->where(array('field' => 'id', 'value' => $id));
            $this->whereOr();
        }

        $data   = $this->getRows();
        $result = array();
        //print_r($data);
        foreach ($data as $object) {

            self::__construct($object);
            $this_clone          = clone $this;
            $result[$object->id] = $this_clone;
        }

        return $result;
    }

    protected function _init()
    {
    }


    public function __get($key)
    {
        if (isset($this->object->$key))
            return $this->object->$key;
        elseif (isset($this->$key)) {
            return $this->$key;
        }
    }

    public function __set($key, $value)
    {
        if (isset($this->$key)) {
            $this->$key = $value;
        } elseif (isset($this->object)) {
            $this->object->$key = $value;
        }

    }


    public function beginTransaction()
    {
        $this->db()->beginTransaction();
    }

    public function commit()
    {
        $this->db()->commit();
    }

    public function rollBack()
    {
        $this->db()->rollBack();
    }

    public static function getTable()
    {
        return static::$table;
    }


    public function removeSafe()
    {

        $this->_removed = 1;

        return $this->save();

    }

    public function remove()
    {

        if ($this::$translated) {
                $this->beginTransaction();
                $DBQueryRemoveLang = $this->db()->prepare("
                            DELETE
                            FROM `" . _DB_PREFIX_ . $this::$table . "_lang`
                            WHERE id_" . \Tools::depluralize(static::$table) . "=:ID
                   ");

                $DBQueryRemoveLang->bindParam(":ID", $this->id, PDO::PARAM_INT);
                $DBQueryRemoveLang->execute();
                $DBQueryRemove = $this->db()->prepare("
                            DELETE
                            FROM `" . _DB_PREFIX_ . $this::$table . "`
                            WHERE id=:ID LIMIT 1
                   ");
                $DBQueryRemove->bindParam(":ID", $this->id, PDO::PARAM_INT);
                $DBQueryRemove->execute();
                $this->commit();

                $this->removedObject = $this->object;
                $this->id            = null;
                $this->object        = (object)array();

                return true;

        } else {

            $DbQueryRemoveObject = $this->db()->prepare("DELETE FROM `" . _DB_PREFIX_ . $this::$table . "` WHERE id=:ID LIMIT 1");
            $DbQueryRemoveObject->bindParam(":ID", $this->id, PDO::PARAM_INT);
            $DbResultRemoveObject = $DbQueryRemoveObject->execute();


            $this->removedObject = $this->object;
            $this->id            = null;
            $this->object        = (object)array();

            return true;
        }


    }

    public function validationFields()
    {
        return true;
    }

    public function getValidationErrors()
    {
        return $this->validateErrors;
    }



    //select : array(
    /*
            'table' => 'таблица из которой делается выборка, если пусто значить главная таблица',
            'field' => ''
          )*/

    //where : array(
    /*
        array(
            'table'   => 'таблица из которой берем значение'
            'field'   => 'поле для сравнения',
            'value'   => 'значение',
            'ctype'   => 'тип стравнения =, LIKE ... ',
            'wtype'   => 'тип сравнения посравнению с предыдущим (по умолчанию AND)'
        ), ...

    )*/

    //join : array(
    /*
     'type' => 'тип прикрепления',
     'table' => 'таблицу которую прикрепляем',
     'on' => array(
              'table1' => 'таблица первого поля (по умолчанию главная таблица)',
              'field1'=>'поле из первой таблицы',
              'field' => 'поле из этой таблицы'
              )
      )*/
    //order : array('table => '', ''type' => 'тип ASC или DESC', 'field' => '')
    //limit : array('from' => '', count => '')

    public function getRowsCount()
    {
        $this->_queryOptions['select'] = array(array('field' => 'id', 'function' => 'count', 'as' => 'count'));
        $this->_queryOptions['limit']  = null;
        $res                           = ($this->getRows());

        return $res[0]->count;
    }


    private function _parseSelect($params)
    {
        $table        = _DB_PREFIX_ . $this::$table;
        $query_select = '';

        if (isset($params['select']) && $params['select']) {
            $query_select .= 'SELECT ';
            $query_select_array = array();
            $select             = $params['select'];

            foreach ($select as $sq) {

                $sq['table'] = (isset($sq['table']) ? _DB_PREFIX_ . $sq['table'] : $table);
                if (isset($sq['prefix'])) {
                    $sq['table'] = $sq['prefix'];
                }

                if (!isset($sq['function'])) {
                    $query_select_array[] = "{$sq['table']}.{$sq['field']}";
                    //     print_r($this->_queryOptions);
                } else {
                    if (strpos($sq['function'], '%field%')) {
                        $sq['function'] = str_replace('%field%', $sq['table'] . '.' . $sq['field'], $sq['function']);
                        // echo $sq['function'];
                        $query_select_array[] = "{$sq['function']}";
                    } else {
                        $query_select_array[] = "{$sq['function']}({$sq['table']}.{$sq['field']})";
                    }
                }
                if (isset($sq['as'])) {
                    $query_select_array[count($query_select_array) - 1] .= " AS {$sq['as']}";
                }

            }
            $query_select .= implode(',', $query_select_array);
        } else {
            $query_select = 'SELECT `' . _DB_PREFIX_ . $this::$table . '`.*';
        }

        return $query_select;
    }

    private function _parseJoin($params)
    {
        $table      = _DB_PREFIX_ . $this::$table;
        $query_join = '';
        //    print_r($params);
        if (isset($params['join'])) {
            foreach ($params['join'] as $join) {
                $join['type'] = (isset($join['type']) ? $join['type'] : 'LEFT');

                $join['table'] = (isset($join['table']) ? _DB_PREFIX_ . $join['table'] : $table);

                if (isset($join['as_table'])) {
                    $join['table'] = $join['as_table'];
                }

                $join['on']['table1'] = (isset($join['on']['table1']) ? _DB_PREFIX_ . $join['on']['table1'] : $table);
                if (isset($join['on']['as_table1'])) {
                    $join['on']['table1'] = $join['on']['as_table1'];
                }

                if (isset($join['on']['as_table1'])) {
                    $join['on']['table1'] = $join['on']['as_table1'];
                }

                $join['as'] = (isset($join['as']) ? $join['as'] : '');
                if ($join['as']) {
                    $query_join .= "$join[type] JOIN $join[table] AS {$join['as']} ON {$join['on']['table1']}.{$join['on']['field1']}={$join['as']}.{$join['on']['field']} ";
                } else {
                    $query_join .= "$join[type] JOIN $join[table] ON {$join['on']['table1']}.{$join['on']['field1']}={$join['table']}.{$join['on']['field']} ";

                }
                // echo $query_join;
            }
        }

        return $query_join;
    }

    private $_whereValues = array();

    private function _parseWhere($params)
    {
        $table        = _DB_PREFIX_ . $this::$table;
        $query_where  = '';
        $where_values = array();
        if (isset($params['where']) && count($params['where']) > 0) {
            $query_where .= ' ';
            $where = $params['where'];


            $has_table = false; // была ли таблица уже
            foreach ($where as $i => $wq) {
                $j    = abs($i - 1);
                $last = (count($where) - 1 == $i ? true : false);
                if (isset($wq['type'])) {
                    if ($wq['val'] == 'scopeon') {

                        if ($has_table && (!isset($where[$j]['type']) || $where[$j]['val'] == 'scopeoff')) { //если предыдущий параметр не таблица
                            $query_where .= ' AND ';
                        }

                        $query_where .= '(';
                    } else if ($wq['val'] == 'scopeoff') {
                        $query_where .= ')';
                        if ($has_table && (!isset($where[$j]['type']) || $where[$j]['val'] == 'scopeoff')) { //если предыдущий параметр не таблица
                            $query_where .= ' AND ';
                        }
                    } else if ($wq['val'] == 'and') {
                        if (!$last)
                            $query_where .= ' AND ';
                    } else if ($wq['val'] == 'or') {
                        if (!$last)
                            $query_where .= ' OR ';
                    }
                    continue;
                }

                if ($has_table && !isset($where[$j]['type'])) {
                    $query_where .= ' AND ';
                }

                $has_table = true;

                if (isset($wq['table']) && $wq['table'] && $wq['table'] != 'alias') {
                    $wq['table'] = (isset($wq['table']) && $wq['table'] ? _DB_PREFIX_ . $wq['table'] : $table);
                } else if (isset($wq['class'])) {
                    $wq['table'] = $wq['class']::$table;}
                else if (isset($wq['table']) && $wq['table'] == 'alias') {
                        $wq['table'] = 'alias';

                } else {
                    $wq['table'] = '';
                }
                $wq['vtype'] = (isset($wq['vtype']) && $wq['vtype'] ? $wq['vtype'] : ' AND ');
                $wq['ctype'] = (isset($wq['ctype']) && $wq['ctype'] ? $wq['ctype'] : '=');
                // $wq['level'] = (isset($wq['level']) ? $wq['level'] : 0);
                // $index = current($where);
                if($wq['table'] == 'alias'){
                    $wq['field'] = "{$wq['field']}";
                }
                 elseif ($wq['table']) {
                     $wq['field'] = $wq['table'] . '.' . $wq['field'];
                 } else {
                    $wq['field'] = "`" . _DB_PREFIX_ . $this::$table . "`.{$wq['field']}";

                }


                $query_where .= " $wq[field] $wq[ctype] ? ";
                $where_values[] = $wq['value'];
            }
            $this->_whereValues = $where_values;
            //  print_r($where);
        } else {
            $query_where = '1';
        }

        return $query_where;
    }

    private function _parseGroupBy($params)
    {
        $table       = _DB_PREFIX_ . $this::$table;
        $query_group = '';
        if (isset($params['group'])) {
            $group          = $params['group'];
            $group['table'] = isset($group['table']) ? $group['table'] : $table;

            if(isset($group['function'])){
                $field = $group['table'].'.'.$group['field'];
                $function = $group['function'];
                $function = str_replace('%field%', $field, $function);

                $query_group = "GROUP BY {$function}";
            } else {
                $query_group = "GROUP BY {$group['table']}.{$group['field']}";
            }
        }

        return $query_group;
    }

    private function _parseOrder($params)
    {
        $table       = _DB_PREFIX_ . $this::$table;
        $query_order = '';
        if (isset($params['order'])) {
            $order          = $params['order'];
            $order['table'] = (isset($order['table']) ? _DB_PREFIX_ . $order['table'] : $table);
            $order['type']  = (isset($order['type']) ? $order['type'] : 'ASC');
            $query_order    = "ORDER BY {$order['table']}.{$order['field']} {$order['type']}";
        } else if ($this->_sortable) {
            $order          = array();
            $order['table'] = $table;
            $order['type']  = 'ASC';
            $query_order    = "ORDER BY {$order['table']}.sort_position {$order['type']}";
        }

        return $query_order;

    }

    private function _parseLimit($params)
    {
        $query_limit = '';
        if (isset($params['limit'])) {
            $limit = $params['limit'];
            if (isset($limit['from'])) {
                $query_limit = "LIMIT {$limit['from']}, {$limit['count']}";
            } else {
                $query_limit = "LIMIT {$limit['count']}";
            }
        }

        return $query_limit;
    }

    public function getRow()
    {
        $r = $this->limit(1)->getRows();
        return isset($r[0]) ? $r[0] : false;
    }

    public function getLanguage()
    {
        return 0;
    }

    public function getRowsRemoved()
    {
        $this->_enableRemovedRows = 1;

        return $this->getRows();
    }

    public function getRowsWithRemoved()
    {
        $this->_enableRemovedRows = 2;

        return $this->getRows();
    }

    public function getRows()
    {
        $this->debugInfo['start'] = microtime(true);

        $id_lang = $this->getLanguage();

        $args = func_get_args();
        if ($this->_enableRemoveSafe) {
            if ($this->_enableRemovedRows == 0) {
                $this->where('_removed', 0);

            } else if ($this->_enableRemovedRows == 1) {
                $this->where('_removed', 1);
                $this->_enableRemovedRows = 0;
            }
        }



        $table = _DB_PREFIX_ . $this::$table;

        $query_select = $this->_parseSelect($this->_queryOptions);
        $query_join   = $this->_parseJoin($this->_queryOptions);
        $query_where  = $this->_parseWhere($this->_queryOptions);
        $query_group  = $this->_parseGroupBy($this->_queryOptions);
        $query_order  = $this->_parseOrder($this->_queryOptions);
        $query_limit  = $this->_parseLimit($this->_queryOptions);


        $this->_lastQueryOptions = $this->_queryOptions;
        $this->_queryOptions     = array();
        $result                  = null;


        if ((static::$translated) && $id_lang) {
            $select = array();

            foreach ($this::$translated as $field) {
                $select[] = "`" . _DB_PREFIX_ . static::$table . "`.`" . $field . "` as `" . $field . "_0`, `" . _DB_PREFIX_ . static::$table . "_lang`.`" . $field . "` as `" . $field . "_" . $id_lang . "`";
            }

            $query = "$query_select," . implode(',', $select) . "  FROM $table $query_join
            LEFT JOIN `" . _DB_PREFIX_ . $this::$table . "_lang` ON `" . _DB_PREFIX_ . $this::$table . "`.`id` = `" . _DB_PREFIX_ . $this::$table . "_lang`.`id_" . Tools::depluralize($this::$table) . "`
            WHERE ($query_where) AND  `" . _DB_PREFIX_ . $this::$table . "_lang`.`id_lang` = " . $id_lang . "
            $query_order
            $query_group
            $query_limit
            ";

            $res = Db::getInstance()->prepare($query);
            $this->debugQuery = $query;
            $res->execute($this->_whereValues);

            $result = ($res->fetchAll(PDO::FETCH_OBJ));

            $this->debugInfo['end'] = microtime(true);

            foreach ($result as &$row) {
                foreach ($this::$translated as $field) {
                    if ($row->{$field . '_' . $id_lang} != '') {
                        $row->$field = $row->{$field . '_' . $id_lang};
                    }
                }
            }
        }

        if (!$result) {
            $query = "$query_select FROM $table $query_join WHERE $query_where $query_order $query_group $query_limit";
            $this->debugQuery = $query;

            try {
                // print_r(Db::getInstance());
                $sth = $this->db()->prepare($query);
                if ($sth->execute($this->_whereValues)) {

                    $result=  ($sth->fetchAll(PDO::FETCH_OBJ));

                    $this->debugInfo['end'] = microtime(true);
                    return $result;

                } else return false;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            return $result;
        }

        return null;
    }

    /**
     * @deprecated deprecated
     */
    public function search($key, $value, $id_lang = 0, $ctype = '=')
    {
        $obj = ($this->where($key, $ctype, $value)->limit(1)->getRows($id_lang));
        if (isset($obj[0])) {
            self::__construct($obj[0]);

            return true;
        } else {
            return false;
        }
    }

    public function where()
    {

        $args = func_get_args();

        if (count($args) == 1 && is_array($args[0])) {
            $params                         = $args[0];
            $this->_queryOptions['where'][] = $params;
        } else if (count($args) == 2) {
            if(strpos($args[0], '.')!==FALSE){
                $dt = explode('.', $args[0]);

                $this->_queryOptions['where'][] = array('field' => $dt[1], 'value' => $args[1], 'table' => $dt[0]);
            } else {
                $this->_queryOptions['where'][] = array('field' => $args[0], 'value' => $args[1]);
            }
        } else if (count($args) == 3) {
            if ($args[0] instanceof ObjectModel) {
                $class                          = $args[0];
                $this->_queryOptions['where'][] = array('field' => $args[1], 'value' => $args[2], 'table' => $class::$table);
            } else {
                $this->_queryOptions['where'][] = array('field' => $args[0], 'ctype' => $args[1], 'value' => $args[2]);
            }
        } else if (count($args) == 4) {
            if ($args[0] instanceof ObjectModel) {
                $class                          = $args[0];
                $this->_queryOptions['where'][] = array('field' => $args[1], 'ctype' => $args[2], 'value' => $args[3], 'table' => $class::$table);
            } else {
                $this->_queryOptions['where'][] = array('table' => $args[0], 'field' => $args[1], 'ctype' => $args[2], 'value' => $args[3]);
            }
        }
        return $this;
    }


    public function whereAnd()
    {
        $this->_queryOptions['where'][] = array('type' => 'parameter', 'val' => 'and');
        return $this;
    }

    public function whereOr()
    {
        $this->_queryOptions['where'][] = array('type' => 'parameter', 'val' => 'or');
        return $this;
    }


    public function whereGroup()
    {
        $f = (func_get_args()[0]);
        $this->_queryOptions['where'][] = array('type' => 'parameter', 'val' => 'scopeon');
        $f($this);
        $this->_queryOptions['where'][] = array('type' => 'parameter', 'val' => 'scopeoff');
        return $this;
    }


    public function order($params = array())
    {
        $args = func_get_args();
        if (count($args) == 1) {
            $this->_queryOptions['order']['field'] = $args[0];

        } else if (count($args) == 2 && is_object($args[0]) && $args[0] instanceof ObjectModel) {
            $class                                 = $args[0];
            $this->_queryOptions['order']['table'] = $class::$table;
            $this->_queryOptions['order']['field'] = $args[1];
        } else if (count($args) == 2) {
            $this->_queryOptions['order']['type']  = $args[1];
            $this->_queryOptions['order']['field'] = $args[0];
        } else if (count($args) == 3) {
            $class                                 = $args[0];
            $this->_queryOptions['order']['table'] = $class::$table;
            $this->_queryOptions['order']['field'] = $args[1];
            $this->_queryOptions['order']['type']  = $args[2];
        }


        return $this;
    }

    public function removeRows()
    {
        $rows = $this->getRows();
        foreach ($rows as $row) {
            $object = new $this($row);
            $object->remove();
        }

        return true;
    }

    public function select()
    {
        //  print_r($this->_queryOptions);
        $args = func_get_args();

        if (count($args) == 1 && !is_array($args[0])) {
            $this->_queryOptions['select'][] = array('field' => $args[0]);
        } else if (count($args) == 2) {
            if ($args[0] instanceof ObjectModel) {
                $class                           = $args[0];
                $this->_queryOptions['select'][] = array('table' => $class::$table, 'field' => $args[1]);
            } else {
                $this->_queryOptions['select'][] = array('prefix' => $args[0], 'field' => $args[1]);
            }
        } else if (count($args) == 3) {
            if ($args[0] instanceof ObjectModel) {
                $class                           = $args[0];
                $this->_queryOptions['select'][] = array('table' => $class::$table, 'field' => $args[1], 'as' => $args[2]);
            } else {
                $this->_queryOptions['select'][] = array('prefix' => $args[0], 'field' => $args[1], 'as' => $args[2]);
            }
        } elseif (is_array($args[0])) {
            $params = $args[0];
            $this->_queryOptions['select'][] = $params;
        }

        return $this;
    }

    public function groupBy()
    {
        $args = func_get_args();

        if(is_array($args[0])){
            $this->_queryOptions['group'] = $args[0];
        } else if (count($args) == 1) { // it's only field name
            $this->_queryOptions['group'] = array('field' => $args[0]);
        }


        return $this;
    }

    /**
     *
     *
     *
     */
    public function join($params = array())
    {
        $args = func_get_args();
        /*
         * $param1 - Класс прикрепляемый
         * $param2 - поле таблицы прикрепляемого класа
         * $param3 - поле главной таблицы

        */
        if (count($args) == 3 && $args[0] instanceof ObjectModel) {
            $class = $args[0];
            //   echo $class::$table;
            $this->_queryOptions['join'][] = array('type' => 'LEFT', 'table' => $class::$table, 'on' => array('field' => $args[2], 'field1' => $args[1]));
        } else if (count($args) == 3 && is_string($args[0]) && is_string($args[1])) {
            $this->_queryOptions['join'][] = array('type' => 'LEFT', 'as_table' => $args[0], 'on' => array('field' => $args[2], 'field1' => $args[1]));
        } else if (count($args) == 4 && $args[0] instanceof ObjectModel && $args[1] instanceof ObjectModel) {
            $class1 = $args[0];
            $class  = $args[1];
            // echo 99;
            $this->_queryOptions['join'][] = array('type' => 'LEFT', 'table' => $class::$table, 'on' => array('table1' => $class1::$table, 'field' => $args[3], 'field1' => $args[2]));
        } else if (count($args) == 4 && $args[0] instanceof ObjectModel) {
            $class                         = $args[0];
            $this->_queryOptions['join'][] = array('type' => 'LEFT', 'table' => $class::$table, 'as' => $args[3], 'on' => array('field' => $args[2], 'field1' => $args[1]));
        } else if (count($args) == 5 && $args[0] instanceof ObjectModel && $args[1] instanceof ObjectModel) {
            $class1 = $args[0];
            $class  = $args[1];
            // echo 99;
            $this->_queryOptions['join'][] = array('type' => 'LEFT', 'table' => $class::$table, 'as' => $args[4], 'on' => array('table1' => $class1::$table, 'field' => $args[3], 'field1' => $args[2]));
        } else if (count($args) == 5 && is_string($args[0]) && $args[1] instanceof ObjectModel) {
            $alias = $args[0];
            $class = $args[1];
            // echo 99;
            $this->_queryOptions['join'][] = array('type' => 'LEFT', 'table' => $class::$table, 'as' => $args[4], 'on' => array('as_table1' => $alias, 'field' => $args[3], 'field1' => $args[2]));
        } else if (count($args) == 1 && is_array($args)) {
            $this->_queryOptions['join'][] = $params;
        }

        return $this;
    }

    public function limit($params = array())
    {
        $args = func_get_args();

        if (count($args) == 1 && !is_array($args[0])) {
            $this->_queryOptions['limit'] = array('count' => $args[0]);
        } else if (count($args) == 2) {
            $this->_queryOptions['limit']          = array();
            $this->_queryOptions['limit']['from']  = $args[0];
            $this->_queryOptions['limit']['count'] = $args[1];
        } else {
            $this->_queryOptions['limit'] = $params;
        }

        return $this;
    }


    public function lastQuery()
    {
        $this->_queryOptions = $this->_lastQueryOptions;

        return $this;
    }


    public function isLoadedObject()
    {
        if (is_object($this->object) && isset($this->object->id) && $this->object->id) {
            return true;
        }

        return false;
    }



    public function save($id_lang = 0)
    {


        if (!$this->id && !empty($this->object)) {
            $key  = array();
            $val  = array();
            $mark = array();

            if ($this->_sortable) {
                $sp                          = $this->select(array('field' => 'sort_position', 'function' => 'MAX', 'as' => 'max'))->getRows();
                $new_position                = ($sp[0]->max) + 1;
                $this->object->sort_position = $new_position;
            }

            foreach ($this->object as $k => $v) {

                $key[]  = $k;
                $val[]  = $v;
                $mark[] = '?';

            }


            try {

                $e = Db::getInstance()->prepare("INSERT INTO `" . _DB_PREFIX_ . $this::$table . "` (`" . implode('`,`', $key) . "`) values (" . implode(',', $mark) . ")");


                $ret = $e->execute($val);

                $this->id         = Db::getInstance()->lastInsertId();
                $this->object->id = $this->id;

                if ($id_lang) {
                    return $this->save($id_lang);
                }

                return $ret;
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
        } else if (!empty($this->object)) {

            if ($id_lang) {
                $lquery = Db::getInstance()->prepare("
                            SELECT *
                            FROM `" . _DB_PREFIX_ . static::$table . "_lang`
                            WHERE `id_lang` = :ID_LANG AND `id_" . static::$table . "` = :ID
                        ");

                $lquery->bindValue("ID_LANG", $id_lang, PDO::PARAM_INT);
                $lquery->bindValue("ID", $this->id, PDO::PARAM_INT);

                $lquery->execute();

                $lang_fetch = $lquery->fetchObject();
                if (!$lang_fetch) {
                    $lang_fetch                                                = new stdClass();
                    $lang_fetch->id_lang                                       = $id_lang;
                    $lang_fetch->{'id_' . \Tools::depluralize(static::$table)} = $this->id;

                    $key   = array();
                    $val   = array();
                    $lmark = array();

                    foreach ($lang_fetch as $k => $v) {

                        $lkey[]  = $k;
                        $lval[]  = $v;
                        $lmark[] = '?';

                    }

                    $res            = Db::getInstance()->prepare("INSERT INTO `" . _DB_PREFIX_ . $this::$table . "_lang` (`" . implode('`,`', $lkey) . "`) values (" . implode(',', $lmark) . ")")->execute($lval);
                    $lang_fetch->id = Db::getInstance()->lastInsertId();

                }

                //  print_r($this->object);
                $lang_array = array();
                foreach (static::$translated as $field) {
                    //  $this->object->{$field.'_'.$id_lang} = $this->object->{$field};
                    $lang_array[$field] = $this->object->{$field};

                    if (isset($this->object->{$field . '_0'})) {
                        $this->object->{$field} = $this->object->{$field . '_0'};
                    } else {
                        unset($this->object->{$field});
                    }

                    unset($this->object->{$field . '_' . $id_lang});
                    unset($this->object->{$field . '_0'});
                }
            }


            foreach ($this->object as $k => $v) {


                $key[] = "`" . $k . "`";
                $val[] = ($v);

            }

            $set = implode("=?,", $key) . "=?";
            try {
                $query = Db::getInstance()->prepare("UPDATE `" . _DB_PREFIX_ . $this::$table . "` SET " . $set . "   WHERE `id`='" . $this->id . "'");

                if ($id_lang) {
                    if ($query->execute($val)) {

                        foreach (static::$translated as $field) {
                            $lang_fetch->$field   = $lang_array[$field];
                            $this->object->$field = $lang_array[$field];
                        }

                        if ($lang_fetch) {
                            $key = array();
                            $val = array();

                            foreach ($lang_fetch as $k => $v) {
                                $key[] = "`" . $k . "`";
                                $val[] = ($v);
                            }

                            $set = implode("=?,", $key) . "=?";

                            return Db::getInstance()->prepare("UPDATE `" . _DB_PREFIX_ . $this::$table . "_lang` SET " . $set . "   WHERE `id`='" . $lang_fetch->id . "'")->execute($val);
                        }
                    }
                } else {
                    //echo (int)$query->execute($val);
                    return $query->execute($val);
                }


            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
        }

        return false;
    }


}

?>