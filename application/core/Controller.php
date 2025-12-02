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
        if ( $user = \application\services\UserService::getCurrentUser() )
        {
            return [
                'role'  => $user->role,
                'id'    => $user->id,
                'email' => $user->email,
                'name'  => $user->name
            ];
        }

        return [ 'role' => 'guest', 'id' => 0 ];
    }

    protected function checkAccess( ?string $action = null ) : void
    {
        $ctx = $this->getUserContext();

        new \application\services\AccessService( $ctx['role'] )
            ->checkAccess( $this->route['controller'], $action ?? ( $this->route['action'] ?? 'index' ) );

        $this->route['_user'] = $ctx;
    }

    protected function requireAuth() : void
    {
        if ( !\application\services\UserService::isLoggedIn() )
        {
            $this->view->redirect( '/auth/login' );
        }
    }

    protected function requireRole( string $role ) : void
    {
        $this->requireAuth();

        if ( \application\services\UserService::getCurrentUser()->role !== $role )
        {
            throw new \application\exceptions\UnauthorizedException( 'Недостаточно прав' );
        }
    }

}
