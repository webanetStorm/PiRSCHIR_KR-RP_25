<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 02.12.2025
 * Time: 21:39
 */

namespace application\services;


class UserService
{

    private static ?\application\models\User $_currentUser = null;


    public static function getCurrentUser() : ?\application\models\User
    {
        if ( self::$_currentUser instanceof \application\models\User )
        {
            return self::$_currentUser;
        }

        return self::$_currentUser = \application\models\User::findById( $_SESSION['user_id'] ?? 0 );
    }

    public static function isLoggedIn() : bool
    {
        return self::getCurrentUser() !== null;
    }

    public static function getId() : ?int
    {
        return self::getCurrentUser()?->id;
    }

    public static function getRole() : string
    {
        return ( $user = self::getCurrentUser() ) ? $user->role : 'guest';
    }

    public static function getName() : string
    {
        return ( $user = self::getCurrentUser() ) ? $user->name : '';
    }

    public static function login( \application\models\User $user ) : void
    {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_role'] = $user->role;

        self::$_currentUser = $user;
    }

    public static function logout() : void
    {
        session_unset();
        session_destroy();

        self::$_currentUser = null;
    }

}
