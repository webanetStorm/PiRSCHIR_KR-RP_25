<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 04.12.2025
 * Time: 16:50
 */

namespace application\core;


class DB
{

    private static ?\Krugozor\Database\Mysql $_connection = null;


    public static function i() : \Krugozor\Database\Mysql
    {
        if ( self::$_connection instanceof \Krugozor\Database\Mysql )
        {
            return self::$_connection;
        }

        return self::$_connection = \Krugozor\Database\Mysql::create( DB_HOST, DB_USER, DB_PASS )
            ->setDatabaseName( DB_NAME )
            ->setCharset( DB_CHARSET );

        /* return self::$_connection = \Krugozor\Database\Mysql::create( 'localhost', 'root', 'root' )
            ->setDatabaseName( 'quelyd' )
            ->setCharset( 'utf8mb4' ); */
    }

}
