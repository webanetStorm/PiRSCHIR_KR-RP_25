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
        $route = '#^' . preg_replace( '/{([a-z]+):([^}]+)}/', '(?P<\1>\2)', $route ) . '$#';

        $this->_routes[$route] = $params;
    }

    public function match() : bool
    {
        $url = trim( $_SERVER['REQUEST_URI'], '/' );

        foreach ( $this->_routes as $route => $params )
        {
            if ( preg_match( $route, $url, $matches ) )
            {
                $routeParams = array_filter( $matches, 'is_string', ARRAY_FILTER_USE_KEY );

                unset( $routeParams[0] );

                $this->_params = array_merge( $params, $routeParams );

                return true;
            }
        }

        return false;
    }

    public function run() : void
    {
        if ( $this->match() )
        {
            $controller = 'application\controllers\\' . ucfirst( $this->_params['controller'] ) . 'Controller';

            if ( class_exists( $controller ) )
            {
                $action = $this->_params['action'] . 'Action';

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
