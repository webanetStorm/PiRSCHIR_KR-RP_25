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

    public function loginAction() : void
    {
        $error = '';

        if ( $_POST )
        {
            try
            {
                $email = trim( $_POST['email'] ?? '' );
                $password = $_POST['password'] ?? '';

                if ( empty( $email ) || empty( $password ) )
                {
                    $error = 'Заполните все поля';
                }
                else
                {
                    if ( $user = \application\models\User::login( $email, $password ) )
                    {
                        $_SESSION['user_id'] = $user->id;
                        $_SESSION['user_email'] = $user->email;
                        $_SESSION['user_name'] = $user->name;
                        $_SESSION['user_role'] = $user->role;

                        $this->view->redirect( '/' );
                    }
                    else
                    {
                        $error = 'Неверный email или пароль';
                    }
                }
            }
            catch ( \Exception $e )
            {
                $error = "Ошибка при входе: {$e->getMessage()}";
            }
        }

        $this->view->render( 'Вход в Quelyd', compact( 'error' ) );
    }

    public function registerAction() : void
    {
        $error = '';

        if ( $_POST )
        {
            try
            {
                $email = trim( $_POST['email'] ?? '' );
                $password = $_POST['password'] ?? '';
                $name = trim( $_POST['name'] ?? '' );

                if ( empty( $email ) || empty( $password ) || empty( $name ) )
                {
                    $error = 'Заполните все поля';
                }
                elseif ( mb_strlen( $password ) < 4 )
                {
                    $error = 'Пароль должен содержать не менее 6 символов';
                }
                else
                {
                    $user = \application\models\User::register( [
                        'email'    => $email,
                        'password' => $password,
                        'name'     => $name,
                        'role'     => 'user'
                    ] );

                    $_SESSION['user_id'] = $user->id;
                    $_SESSION['user_email'] = $user->email;
                    $_SESSION['user_name'] = $user->name;
                    $_SESSION['user_role'] = $user->role;

                    $this->view->redirect( '/' );
                }

            }
            catch ( \application\exceptions\ValidationException $e )
            {
                $error = $e->getMessage();
            }
            catch ( \Exception $e )
            {
                $error = 'Ошибка при регистрации: ' . $e->getMessage();
            }
        }

        $this->view->render( 'Регистрация в Quelyd', compact( 'error' ) );
    }

    #[NoReturn]
    public function logoutAction() : void
    {
        session_unset();
        session_destroy();

        $this->view->redirect( '/' );
    }

    public function profileAction() : void
    {
        $this->checkAccess();

        $user = \application\models\User::findById( $this->getCurrentUserId() );

        $this->view->render( 'Профиль пользователя', compact( 'user' ) );
    }



}
