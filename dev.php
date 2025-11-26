<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 16:01
 */

ini_set( 'display_errors', 1 );
error_reporting( E_ALL );

function debug( mixed $var, bool $exit = true ) : void
{
    echo '<pre>';
    var_dump( $var );
    echo '</pre>';

    if ( $exit )
    {
        exit;
    }
}
