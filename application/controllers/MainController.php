<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 16:45
 */

namespace application\controllers;


class MainController extends \application\core\Controller
{

    public function indexAction() : void
    {
        $this->view->render( 'Главная', [ 'isLoggedIn'  => \application\services\UserService::isLoggedIn() ] );
    }

}
