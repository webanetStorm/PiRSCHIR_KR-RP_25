<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 21:01
 */

namespace application\services;


class AccessService
{

    private string $_currentRole;


    public function __construct( ?string $role = 'guest' )
    {
        $this->_currentRole = $role;
    }

    public function checkAccess( string $controller, string $action ) : void
    {
        $rules = require 'application/config/rbac.php';

        $key = $controller . '.' . $action;

        if ( !isset( $rules[$key] ) )
        {
            throw new \LogicException( "Правило RBAC не определено $key" );
        }

        if ( !in_array( $this->_currentRole, $rules[$key] ) )
        {
            throw new \application\exceptions\UnauthorizedException( "У роли $this->_currentRole нет доступа к $key" );
        }
    }

    public function isGuest() : bool
    {
        return $this->_currentRole === 'guest';
    }

    public function isUser() : bool
    {
        return in_array( $this->_currentRole, [ 'user', 'admin' ] );
    }

    public function isAdmin() : bool
    {
        return $this->_currentRole === 'admin';
    }

}
