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

    public function loginAction() : void
    {
        try
        {
            $data = $this->getJsonInput();

            $email = trim( $data['email'] ?? '' );
            $password = $data['password'] ?? '';

            if ( empty( $email ) || empty( $password ) )
            {
                $this->error( 'Заполните все поля', [], 400 );

                return;
            }

            if ( !( $user = \application\models\User::login( $email, $password ) ) )
            {
                $this->error( 'Неверный email или пароль', [], 401 );

                return;
            }

            \application\services\UserService::login( $user );

            $this->success( [
                'token' => $this->generateToken( $user ),
                'user'  => $user->toArray()
            ], 'Успешный вход' );

        }
        catch ( \Exception $e )
        {
            $this->error( 'Ошибка при входе: ' . $e->getMessage(), [], 500 );
        }
    }

    public function registerAction() : void
    {
        try
        {
            $data = $this->getJsonInput();

            $email = trim( $data['email'] ?? '' );
            $password = $data['password'] ?? '';
            $name = trim( $data['name'] ?? '' );

            if ( empty( $email ) || empty( $password ) || empty( $name ) )
            {
                $this->error( 'Заполните все поля', [], 400 );

                return;
            }

            if ( mb_strlen( $password ) < 4 )
            {
                $this->error( 'Пароль должен содержать не менее 4 символов', [], 400 );

                return;
            }

            $user = \application\models\User::register( [
                'email'    => $email,
                'password' => $password,
                'name'     => $name,
                'role'     => 'user'
            ] );

            \application\services\UserService::login( $user );

            $this->success( [
                'token' => $this->generateToken( $user ),
                'user'  => $user->toArray()
            ], 'Регистрация успешна' );

        }
        catch ( \application\exceptions\ValidationException $e )
        {
            $this->error( $e->getMessage(), [], 422 );
        }
        catch ( \Exception $e )
        {
            $this->error( 'Ошибка при регистрации: ' . $e->getMessage(), [], 500 );
        }
    }

    public function logoutAction() : void
    {
        try
        {
            \application\services\UserService::logout();
            $this->success( [], 'Успешный выход из системы' );
        }
        catch ( \Exception $e )
        {
            $this->error( 'Ошибка при выходе: ' . $e->getMessage() );
        }
    }

    public function profileAction() : void
    {
        try
        {
            $this->requireAuth();

            if ( !( $user = \application\services\UserService::getCurrentUser() ) )
            {
                $this->error( 'Пользователь не найден', [], 404 );

                return;
            }

            $this->success( $user->toArray() );
        }
        catch ( \application\exceptions\UnauthorizedException $e )
        {
            $this->error( $e->getMessage(), [], 401 );
        }
    }

    private function generateToken( \application\models\User $user ) : string
    {
        $payload = [
            'user_id' => $user->id,
            'email'   => $user->email,
            'role'    => $user->role,
            'exp'     => time() + ( 24 * 60 * 60 )
        ];

        return base64_encode( json_encode( $payload ) );
    }

    public static function validateToken( string $token ) : ?array
    {
        try
        {
            $decoded = json_decode( base64_decode( $token ), true );

            if ( !$decoded || !isset( $decoded['exp'] ) || $decoded['exp'] < time() )
            {
                return null;
            }

            return $decoded;
        }
        catch ( \Exception $e )
        {
            return null;
        }
    }

}
