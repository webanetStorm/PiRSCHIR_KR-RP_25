<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 16:08
 */

namespace application\core;


class Router
{

    private array $_routes = array();

    private array $_params = array();


    public function __construct()
    {
        $routes = require 'application/config/routes.php';

        foreach ( $routes as $route => $params )
        {
            $this->add( $route, $params );
        }
    }

    public function add( string $route, array $params ) : void
    {
        $this->_routes['#^' . preg_replace( '/\{([a-z]+):([^}]+)}/', '(?P<\1>\2)', $route ) . '$#'] = $params;
    }

    public function match() : bool
    {
        $url = trim( $_SERVER['REQUEST_URI'], '/' );

        if ( ( $pos = strpos( $url, '?' ) ) !== false )
        {
            $url = substr( $url, 0, $pos );
        }

        foreach ( $this->_routes as $route => $params )
        {
            if ( preg_match( $route, $url, $matches ) )
            {
                $this->_params = array_merge( $params, array_filter( $matches, fn( $key ) => is_string( $key ), ARRAY_FILTER_USE_KEY ) );

                return true;
            }
        }

        return false;
    }

    public function run() : void
    {
        if ( $this->match() )
        {
            $controller = str_starts_with( $this->_params['controller'], 'api/' )
                ? 'application\controllers\api\\' . ucfirst( str_replace( 'api/', '', $this->_params['controller'] ) ) . 'Controller'
                : 'application\controllers\\' . ucfirst( $this->_params['controller'] ) . 'Controller';

            $action = $this->_params['action'] . 'Action';

            if ( class_exists( $controller ) )
            {
                if ( method_exists( $controller, $action ) )
                {
                    new $controller( $this->_params )->$action();
                }
                else
                {
                    View::errorCode( 404 );
                }
            }
            else
            {
                View::errorCode( 404 );
            }
        }
        else
        {
            View::errorCode( 404 );
        }
    }

}
