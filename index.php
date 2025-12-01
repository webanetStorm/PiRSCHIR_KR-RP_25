<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 15:55
 */

require __DIR__ . '/dev.php';
require __DIR__ . '/conf_global.php';
require __DIR__ . '/vendor/autoload.php';

spl_autoload_register( function( string $class )
{
    if ( file_exists( $path = str_replace( '\\', '/', $class . '.php' ) ) )
    {
        require_once $path;
    }
} );

session_start();

new \application\core\Router()->run();
