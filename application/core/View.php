<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 16:25
 */

namespace application\core;

use JetBrains\PhpStorm\NoReturn;


class View
{

    private string $_layout = 'main';

    private array $_route;


    public function __construct( array $route )
    {
        $this->_route = $route;
    }

    public function render( string $title, array $vars = [] ) : void
    {
        extract( $vars );

        if ( file_exists( $path = 'application/views/' . $this->_route['controller'] . '/' . $this->_route['action'] . '.php' ) )
        {
            ob_start();
            require $path;
            $content = ob_get_clean();

            $controllerCss = "{$this->_route['controller']}.css";
            $cssFiles = [ 'global.css' ];

            if ( file_exists( __DIR__ . '/../../public/styles/' . $controllerCss ) )
            {
                $cssFiles[] = $controllerCss;
            }

            require 'application/views/layouts/' . $this->_layout . '.php';
        }
    }

    #[NoReturn]
    public function redirect( string $url ) : void
    {
        header( 'location: ' . $url );
        exit;
    }

    #[NoReturn]
    public static function errorCode( int $code ) : void
    {
        http_response_code( $code );

        if ( file_exists( $path = 'application/views/errors/' . $code . '.php' ) )
        {
            require $path;
        }

        exit;
    }

}
