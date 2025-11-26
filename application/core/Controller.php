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

    protected function getUserContext() : array
    {
        if ( preg_match( '/Bearer\s+(\S+)/', $_SERVER['HTTP_AUTHORIZATION'] ?? '', $m ) )
        {
            $payload = $this->decodeJwt( $m[1] );

            return [ 'role' => $payload['role'] ?? 'guest', 'id' => (int)( $payload['sub'] ?? 0 ) ];
        }

        if ( isset( $_SESSION['user_id'], $_SESSION['role'] ) )
        {
            return [ 'role' => $_SESSION['role'], 'id' => (int)$_SESSION['user_id'] ];
        }

        return [ 'role' => 'guest', 'id' => 0 ];
    }

    protected function checkAccess( ?string $action = null ) : void
    {
        $ctx = $this->getUserContext();
        $ctrl = $this->route['controller'] ?? 'main';
        $act = $action ?? ( $this->route['action'] ?? 'index' );

        $access = new \application\services\AccessService( $ctx['role'] );
        $access->checkAccess( $ctrl, $act );

        $this->route['_user'] = $ctx;
    }

    protected function getCurrentUserId() : int
    {
        return $this->route['_user']['id'] ?? throw new \applications\exceptions\UnauthorizedException;
    }

    private function decodeJwt( string $token ) : array
    {
        [ $header64, $payload64 ] = explode( '.', $token, 3 ) + [ null, null ];

        if ( !$payload64 )
        {
            return [];
        }

        $payload = json_decode( base64_decode( strtr( $payload64, '-_', '+/' ) ), true );

        return is_array( $payload ) ? $payload : [];
    }

}
