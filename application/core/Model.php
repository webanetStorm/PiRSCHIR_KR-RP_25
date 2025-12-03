<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 16:10
 */

namespace application\core;


abstract class Model
{

    private static ?\Krugozor\Database\Mysql $_connection = null;


    protected static function db() : \Krugozor\Database\Mysql
    {
        if ( self::$_connection instanceof \Krugozor\Database\Mysql )
        {
            return self::$_connection;
        }

        return self::$_connection = \Krugozor\Database\Mysql::create( DB_HOST, DB_USER, DB_PASS )
            ->setDatabaseName( DB_NAME )
            ->setCharset( DB_CHARSET );
    }

    abstract public function validate();

}
