<?php
/**
 * Created by PhpStorm.
 * Client: Viktor
 * Date: 28.02.15
 * Time: 18:09
 */

namespace System\Db\Schema;

use System\Db\Db;

class Table
{

    public $fields = array();
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function increment($field)
    {
        $this->fields[$field] = array('type' => 'increment', 'nullable' => false, 'default' => false);

        return $this;
    }

    public function int($field, $length = 10)
    {
        $this->fields[$field] = array('type' => 'int', 'length' => $length, 'nullable' => false, 'default' => '0');

        return $this;
    }

    public function float($field, $length = 10)
    {
        $this->fields[$field] = array('type' => 'float', 'length' => $length, 'nullable' => false, 'default' => '0');

        return $this;
    }

    public function date($field)
    {
        $this->fields[$field] = array('type' => 'date', 'nullable' => false, 'default' => "'0000-00-00'");

        return $this;
    }

    public function timestamp($field)
    {
        $this->fields[$field] = array('type' => 'timestamp', 'nullable' => false, 'default' => 'NOW()');

        return $this;
    }

    public function text($field, $length = 800)
    {
        $this->fields[$field] = array('type' => 'text', 'length' => $length, 'nullable' => true, 'default' => "NULL");

        return $this;
    }

    public function string($field, $length = 300, $default = "''")
    {
        $this->fields[$field] = array('type' => 'varchar', 'length' => $length, 'nullable' => false, 'default' => $default);

        return $this;
    }

    public function nullable()
    {
        end($this->fields)['nullable'] = true;

        return $this;
    }

    public function def($value)
    {
        end($this->fields)['default'] = $value;
    }

    public function bool($field, $default = '0')
    {
        $this->fields[$field] = array('type' => 'bool', 'length' => '1', 'nullable' => false, 'default' => $default);

        return $this;
    }


    public function create()
    {
        $table_name = _DB_PREFIX_ . $this->name;

        $query_create = 'CREATE TABLE IF NOT EXISTS ';

        $query_create .= ' `' . $table_name . '` (';

        $i = 0;
        foreach ($this->fields as $field => $params) {
            $query_create .= '`' . $field . '` ' . self::getFieldType($params) . ' ' . (!$params['nullable'] ? 'NOT NULL' : '') . ' ' . ($params['default'] !== false ? 'DEFAULT ' . $params['default'] : '') . ' ' . ($params['type'] == 'increment' ? 'AUTO_INCREMENT' : '');
            $query_create .= ', ';
            if ($i != count($this->fields) - 1) {

            } else {
                $query_create .= ' PRIMARY KEY (`id`) ';
            }

            $i++;
        }
        $query_create .= ') CHARACTER SET utf8 COLLATE utf8_general_ci;';

        //   echo $query_create;

        return Db::getInstance()->prepare($query_create)->execute();

        // echo '<pre>', $query_create, '</pre>';

    }

    public function drop()
    {
        $table_name = _DB_PREFIX_ . $this->name;

        $query_create = ' DROP TABLE IF EXISTS ' . $table_name;

        return Db::getInstance()->prepare($query_create)->execute();
    }

    protected static function getFieldType($field_params)
    {
        switch ($field_params['type']) {
            case 'int' :
                if ($field_params['length']) {
                    return 'INT(' . $field_params['length'] . ')';
                }
                break;
            case 'float' :
                if ($field_params['length']) {
                    return 'FLOAT(' . $field_params['length'] . ')';
                }
                break;
            case 'increment':
                return 'INT(10)';
                break;
            case 'text':
                return 'TEXT';
                break;
            case 'varchar' :
                if ($field_params['length']) {
                    return 'VARCHAR(' . $field_params['length'] . ')';
                }
                break;
            case 'date' :
                return 'DATE';

                break;
            case 'bool':
                return 'TINYINT';
                break;
            case 'timestamp':
                return 'TIMESTAMP';
        }
    }


} 