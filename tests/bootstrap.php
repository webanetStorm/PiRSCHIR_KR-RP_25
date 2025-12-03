<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 03.12.2025
 * Time: 12:00
 */

const DB_HOST = 'sqlite';
const DB_NAME = ':memory:';
const DB_USER = '';
const DB_PASS = '';
const APP_NAME = 'Quelyd Test';
const DEV_MODE = true;

require __DIR__ . '/../dev.php';
require __DIR__ . '/../vendor/autoload.php';

spl_autoload_register( function( string $class )
{
    if ( file_exists( $path = str_replace( '\\', '/', '../' . $class . '.php' ) ) )
    {
        require_once $path;
    }
} );

session_start();
