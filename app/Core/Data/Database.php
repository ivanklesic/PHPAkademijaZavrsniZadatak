<?php


namespace App\Core\Data;


use App\Core\Config;

class Database extends  \PDO
{
    private static $instance;

    private function __clone()
    {
    }

    private function __construct($remote = null)
    {

        $dbConfig = Config::get($remote ? 'db_remote' : 'db_local');

        $dsn = 'mysql:host='.$dbConfig['host'].';dbname='.$dbConfig['name'].';charset=utf8';

        parent::__construct($dsn, $dbConfig['user'], $dbConfig['password']);

        $this->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);

    }

    public static function getInstance()
    {
        if (static::$instance === null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

}