<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 15:55
 */

require_once __DIR__ . '/dev.php';
require_once __DIR__ . '/conf_global.php';
require_once __DIR__ . '/vendor/autoload.php';

spl_autoload_register( function( $class )
{
    if ( file_exists( $path = str_replace( '\\', '/', $class . '.php' ) ) )
    {
        require $path;
    }
} );

session_start();

new \application\core\Router()->run();