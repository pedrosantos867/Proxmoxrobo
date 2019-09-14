<?php

namespace System\Db;

use System\Config;


class Db extends \PDO
{
    protected static $instance;

    public function __construct()
    {
        $config = new Config();

        $dbhost = $config->db_host;
        $dbname = $config->db_name;
        $dbuser = $config->db_username;
        $dbpass = $config->db_pass;

        try {
            parent::__construct('mysql:host=' . $dbhost . ';dbname=' . $dbname, $dbuser, $dbpass, array(
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ));
        } catch (\PDOException $pdo) {
            exit($pdo->getMessage());
        }

    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) self::$instance = new Db();

        return self::$instance;
    }


}