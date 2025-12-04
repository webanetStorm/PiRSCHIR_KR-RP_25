<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 03.12.2025
 * Time: 0:24
 */

namespace application\controllers\api;


class AuthController extends ApiController
{

    public function actionLogin() : void
    {
        try
        {
            $data = $this->getJsonInput();

            $user = $this->userService->login( trim( $data['email'] ?? '' ), trim( $data['password'] ?? '' ) );

            $this->success( [
                'token' => $this->generateToken( $user ),
                'user'  => $user->toArray()
            ], 'Успешная авторизация' );
        }
        catch ( \Exception $e )
        {
            $this->error( "Ошибка при входе: {$e->getMessage()}", $e->getCode() );
        }
    }

    public function actionRegister() : void
    {
        try
        {
            $data = $this->getJsonInput();

            $data = [
                'email'    => trim( $data['email'] ?? '' ),
                'password' => trim( $data['password'] ?? '' ),
                'name'     => trim( $data['name'] ?? '' ),
            ];

            $user = $this->userService->register( $data );

            $this->success( [
                'token' => $this->generateToken( $user ),
                'user'  => $user->toArray()
            ], 'Успешная регистрация' );

        }
        catch ( \Exception $e )
        {
            $this->error( "Ошибка при регистрации: {$e->getMessage()}", $e->getCode() );
        }
    }

    public function actionProfile() : void
    {
        try
        {
            $this->checkAccess();

            $this->success( $this->currentUser->toArray() );
        }
        catch ( \Exception $e )
        {
            $this->error( $e->getMessage(), $e->getCode() );
        }
    }

}
