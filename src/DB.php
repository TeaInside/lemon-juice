<?php

class DB
{
    public static function __callStatic($method, $param)
    {
        return (new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS))->{$method}(...$param);
    }

    public static function pdo()
    {
        return (new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS));
    }

    public static function pdoInstance()
    {
        return (new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS));
    }
}
