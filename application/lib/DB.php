<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 16:11
 */

namespace application\lib;


class DB
{

    private static ?\Krugozor\Database\Mysql $_connection = null;


    private function __construct()
    {
        return self::$_connection = \Krugozor\Database\Mysql::create( $_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'] )
            ->setDatabaseName( $_ENV['DB_NAME'] )
            ->setCharset( 'utf8mb4' );
    }

    public static function i() : \Krugozor\Database\Mysql
    {
        if ( self::$_connection instanceof self )
        {
            return self::$_connection;
        }

        return self::$_connection = new self::$_connection;
    }

}
