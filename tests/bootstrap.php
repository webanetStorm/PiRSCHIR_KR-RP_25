<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 03.12.2025
 * Time: 12:00
 */

require __DIR__ . '/../vendor/autoload.php';

spl_autoload_register( function( string $class )
{
    if ( file_exists( $path = str_replace( '\\', '/', '../' . $class . '.php' ) ) )
    {
        require_once $path;
    }
} );

session_start();
