<?php
/**
 * Created by PhpStorm.
 * Client: Viktor
 * Date: 28.02.15
 * Time: 17:57
 */

namespace System\Db\Schema;

use System\Db\Db;

class Schema
{

    public static function create($table_name, $callback)
    {
        $table = new Table($table_name);
        $callback($table);
        $table->create();

        return $table;
    }

    public static function table($table_name, $callback)
    {
        $table = new Table($table_name);
        $callback($table);

        return $table;
    }

    public static function drop($table_name)
    {
        $table = new Table($table_name);
        $table->drop();
    }


}