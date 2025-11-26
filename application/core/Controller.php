<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 16:34
 */

namespace application\core;


abstract class Controller
{

    public array $route;

    public View $view;


    public function __construct( array $route )
    {
        $this->route = $route;
        $this->view = new View( $route );
    }

}
