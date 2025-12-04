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

    protected ?\application\repositories\UserRepository $userRepository = null;

    protected ?\application\services\UserService $userService = null;

    protected ?\application\models\User $currentUser = null;


    public function __construct( array $route )
    {
        $this->setJsonResponse();
        $this->setupCors();

        $this->route = $route;
        $this->userService = new \application\services\UserService( $this->userRepository = new \application\repositories\UserRepository );
    }

    /**
     * @throws \application\exceptions\UnauthorizedException
     * @throws \Krugozor\Database\MySqlException
     */
    private function requireApiAuth() : void
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if ( !str_starts_with( $authHeader, 'Bearer ' ) )
        {
            throw new \application\exceptions\UnauthorizedException( 'Требуется авторизация' );
        }

        if ( !( $tokenData = $this->validateToken( substr( $authHeader, 7 ) ) ) )
        {
            throw new \application\exceptions\UnauthorizedException( 'Неверный или просроченный токен' );
        }

        if ( !( $this->currentUser = $this->userRepository->findById( $tokenData['user_id'] ) ) )
        {
            throw new \application\exceptions\UnauthorizedException( 'Пользователь не найден' );
        }
    }

    /**
     * @throws \application\exceptions\UnauthorizedException
     * @throws \Krugozor\Database\MySqlException
     * @throws \application\exceptions\DomainException
     * @throws \application\exceptions\ForbiddenException
     */
    protected function checkAccess() : void
    {
        $this->requireApiAuth();

        new \application\services\AccessService( $this->currentUser?->role ?? 'guest' )->check( $this->route['controller'], $this->route['action'] ?? 'index' );
    }

    private function setupCors() : void
    {
        header( 'Access-Control-Allow-Origin: *' );
        header( 'Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS' );
        header( 'Access-Control-Allow-Headers: Content-Type, Authorization' );

        if ( $_SERVER['REQUEST_METHOD'] === 'OPTIONS' )
        {
            http_response_code( 200 );

            exit;
        }
    }

    private function setJsonResponse() : void
    {
        header( 'Content-Type: application/json; charset=utf-8' );
    }

    protected function getJsonInput() : array
    {
        return is_array( $data = json_decode( file_get_contents( 'php://input' ), true ) ) ? $data : [];
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
    protected function error( string $message, int $code = 400 ) : void
    {
        $this->statusCode = $code;

        $this->response = [ 'success' => false, 'message' => $message ];

        $this->sendResponse();
    }

    #[NoReturn]
    protected function notFound() : void
    {
        $this->error( 'Ресурс не найден', 404 );
    }

    protected function generateToken( \application\models\User $user ) : string
    {
        $payload = [
            'user_id' => $user->id,
            'email'   => $user->email,
            'role'    => $user->role,
            'exp'     => time() + ( 24 * 60 * 60 )
        ];

        return base64_encode( json_encode( $payload ) );
    }

    private function validateToken( string $token ) : ?array
    {
        $decoded = json_decode( base64_decode( $token ), true );

        if ( $decoded && isset( $decoded['exp'] ) && $decoded['exp'] > time() )
        {
            return $decoded;
        }

        return null;
    }

}
