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

        return self::$_connection = \Krugozor\Database\Mysql::create( $_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'] )
            ->setDatabaseName( $_ENV['DB_NAME'] )
            ->setCharset( 'utf8mb4' );
    }

    abstract public function validate();

}
