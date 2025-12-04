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

    private string $_role = 'guest';


    public function __construct( string $role )
    {
        $this->_role = $role;
    }

    /**
     * @throws \application\exceptions\DomainException
     * @throws \application\exceptions\ForbiddenException
     */
    public function check( string $controller, string $action ) : void
    {
        $rules = require __DIR__ . '/../../application/config/rbac.php';

        if ( !isset( $rules[$key = "$controller.$action"] ) )
        {
            throw new \application\exceptions\DomainException( "Правило RBAC не определено: $key" );
        }

        if ( !in_array( $this->_role, $rules[$key] ) )
        {
            throw new \application\exceptions\ForbiddenException( "Нет доступа: $key" );
        }
    }

    public function isGuest() : bool
    {
        return $this->_role === 'guest';
    }

    public function isAdmin() : bool
    {
        return $this->_role === 'admin';
    }

    public function isUser() : bool
    {
        return in_array( $this->_role, [ 'user', 'admin' ] );
    }

}
