<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 21:31
 */

namespace application\models;


class User extends \application\core\Model
{

    protected const string TABLE = 'users';


    public int $id;

    public string $email;

    public string $password_hash;

    public string $name;

    public string $role = 'user';


    public static function create( array $data ) : self
    {
        $user = new self;

        $user->email = $data['email'];
        $user->password_hash = $data['password_hash'];
        $user->name = $data['name'] ?? '';
        $user->role = $data['role'] ?? 'user';

        return $user;
    }

    public function validate()
    {
    }

    public static function login( string $email, string $password ) : ?self
    {
        $row = self::db()->query( "SELECT * FROM `users` WHERE `email` = '?s' LIMIT 1", $email )->fetchAssoc();

        if ( !$row || !password_verify( $password, $row['password_hash'] ) )
        {
            return null;
        }

        $user = self::create( $row );
        $user->id = (int)$row['id'];

        return $user;
    }

    public function toArray() : array
    {
        return [
            'id'    => $this->id,
            'email' => $this->email,
            'name'  => $this->name,
            'role'  => $this->role,
        ];
    }

}
