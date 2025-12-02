<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 02.12.2025
 * Time: 23:49
 */

namespace application\controllers\api;


abstract class ApiController extends \application\core\Controller
{

    protected array $response = [];

    protected int $statusCode = 200;


    public function __construct( array $route )
    {
        parent::__construct( $route );

        $this->setupCors();
        $this->setJsonResponse();
        $this->checkTokenAuth();
    }

    protected function checkTokenAuth() : void
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if ( str_starts_with( $authHeader, 'Bearer ' ) )
        {
            if ( $tokenData = \application\controllers\api\AuthController::validateToken( substr( $authHeader, 7 ) ) )
            {
                if ( $user = \application\models\User::findById( $tokenData['user_id'] ) )
                {
                    $_SESSION['user_id'] = $user->id;
                    $_SESSION['user_email'] = $user->email;
                    $_SESSION['user_name'] = $user->name;
                    $_SESSION['user_role'] = $user->role;
                }
            }
        }
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

    protected function requireAuth() : void
    {
        if ( !\application\services\UserService::isLoggedIn() )
        {
            throw new \application\exceptions\UnauthorizedException( 'Требуется авторизация' );
        }
    }

    protected function getJsonInput() : array
    {
        $data = json_decode( file_get_contents( 'php://input' ), true );

        return is_array( $data ) ? $data : [];
    }

    protected function sendResponse() : void
    {
        http_response_code( $this->statusCode );

        echo json_encode( $this->response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );

        exit;
    }

    protected function success( array $data = [], string $message = '' ) : void
    {
        $this->response = [
            'success' => true,
            'message' => $message,
            'data'    => $data
        ];

        $this->sendResponse();
    }

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

    protected function validationError( array $errors ) : void
    {
        $this->error( 'Ошибка валидации', $errors, 422 );
    }

    protected function notFound() : void
    {
        $this->error( 'Ресурс не найден', [], 404 );
    }

    protected function forbidden() : void
    {
        $this->error( 'Доступ запрещен', [], 403 );
    }

}
