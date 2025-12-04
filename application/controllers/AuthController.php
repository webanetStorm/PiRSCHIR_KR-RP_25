<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 28.11.2025
 * Time: 20:26
 */

namespace application\controllers;


use JetBrains\PhpStorm\NoReturn;


class AuthController extends \application\core\Controller
{

    public function actionLogin() : void
    {
        if ( $_POST )
        {
            try
            {
                $this->userService->login( trim( $_POST['email'] ?? '' ), trim( $_POST['password'] ?? '' ) );

                $this->view->redirect( 'profile' );
            }
            catch ( \application\exceptions\ValidationException $e )
            {
                $this->view->render( 'Вход', [ 'error' => $e->getMessage() ] );

                return;
            }
        }

        $this->view->render( 'Вход' );
    }

    public function actionRegister() : void
    {
        if ( $_POST )
        {
            try
            {
                $data = [
                    'email'    => trim( $_POST['email'] ?? '' ),
                    'password' => trim( $_POST['password'] ?? '' ),
                    'name'     => trim( $_POST['name'] ?? '' ),
                ];

                $this->userService->register( $data );

                $this->view->redirect( 'login' );
            }
            catch ( \application\exceptions\ValidationException $e )
            {
                $this->view->render( 'Регистрация', [ 'error' => $e->getMessage() ] );

                return;
            }
        }

        $this->view->render( 'Регистрация' );
    }


    #[NoReturn]
    public function actionLogout() : void
    {
        $this->userService->logout();

        $this->view->redirect( '/' );
    }

    public function actionProfile() : void
    {
        $user = $this->currentUser;

        $this->view->render( 'Профиль пользователя', compact( 'user' ) );
    }

}
