<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 02.12.2025
 * Time: 23:49
 */

namespace application\controllers\api;


use JetBrains\PhpStorm\NoReturn;


abstract class ApiController
{

    protected array $route;

    protected array $response = [];

    protected int $statusCode = 200;

    protected ?\application\models\User $user = null;


    public function __construct( array $route )
    {
        $this->route = $route;
        $this->setupCors();
        $this->setJsonResponse();
    }

    protected function requireApiAuth() : void
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if ( !str_starts_with( $authHeader, 'Bearer ' ) )
        {
            throw new \application\exceptions\UnauthorizedException( 'Требуется авторизация' );
        }

        $token = substr( $authHeader, 7 );
        $tokenData = AuthController::validateToken( $token );

        if ( !$tokenData )
        {
            throw new \application\exceptions\UnauthorizedException( 'Неверный или просроченный токен' );
        }

        $user = \application\models\User::findById( $tokenData['user_id'] );

        if ( !$user )
        {
            throw new \application\exceptions\UnauthorizedException( 'Пользователь не найден' );
        }

        $this->user = $user;
    }

    protected function checkAccess( string $action = 'index' ) : void
    {
        $this->requireApiAuth();

        new \application\services\AccessService( $this->user->role ?? 'guest' )->checkAccess( $this->route['controller'], $action );
    }

    protected function setupCors() : void
    {
        header( 'Access-Control-Allow-Origin: *' );
        header( 'Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS' );
        header( 'Access-Control-Allow-Headers: Content-Type, Authorization' );

        if ( $_SERVER['REQUEST_METHOD'] === 'OPTIONS' )
        {
            http_response_code( 200 );
            exit;
        }
    }

    protected function setJsonResponse() : void
    {
        header( 'Content-Type: application/json; charset=utf-8' );
    }

    protected function getJsonInput() : array
    {
        $data = json_decode( file_get_contents( 'php://input' ), true );

        return is_array( $data ) ? $data : [];
    }

    #[NoReturn]
    protected function sendResponse() : void
    {
        http_response_code( $this->statusCode );

        echo json_encode( $this->response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );

        exit;
    }

    #[NoReturn]
    protected function success( array $data = [], string $message = '' ) : void
    {
        $this->response = [
            'success' => true,
            'message' => $message,
            'data'    => $data
        ];

        $this->sendResponse();
    }

    #[NoReturn]
    protected function error( string $message, array $errors = [], int $code = 400 ) : void
    {
        $this->statusCode = $code;

        $this->response = [
            'success' => false,
            'message' => $message,
            'errors'  => $errors
        ];

        $this->sendResponse();
    }

    #[NoReturn]
    protected function validationError( array $errors ) : void
    {
        $this->error( 'Ошибка валидации', $errors, 422 );
    }

    #[NoReturn]
    protected function notFound() : void
    {
        $this->error( 'Ресурс не найден', [], 404 );
    }

    #[NoReturn]
    protected function forbidden() : void
    {
        $this->error( 'Доступ запрещен', [], 403 );
    }

}
