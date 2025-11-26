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
        $rules = require __DIR__ . '/../config/rbac.php';

        $key = str_replace( '\\', '.', $controller ) . '.' . $action;

        if ( !isset( $rules[$key] ) )
        {
            throw new \LogicException( "RBAC rule not defined for $key" );
        }

        if ( !in_array( $this->_currentRole, $rules[$key] ) )
        {
            throw new \applications\exceptions\UnauthorizedException( "Access denied: $this->_currentRole → $key" );
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
