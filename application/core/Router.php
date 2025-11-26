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
            if ( preg_match( $route, $url ) )
            {
                $this->_params = $params;

                return true;
            }
        }

        return false;
    }

    public function run() : void
    {
        if ( $this->match() )
        {
            $path = 'application\controllers\\' . ucfirst( $this->_params['controller'] ) . 'Controller';

            if ( class_exists( $path ) )
            {

                $action = $this->_params['action'] . 'Action';

                if ( method_exists( $path, $action ) )
                {
                    $controller = new $path( $this->_params );
                    $controller->$action();
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
