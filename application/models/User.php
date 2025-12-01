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

    public string $role;


    public static function create( array $data ) : self
    {
        $user = new self;

        $user->email = $data['email'];
        $user->password_hash = $data['password_hash'] ?? '';
        $user->name = $data['name'] ?? '';
        $user->role = $data['role'] ?? 'user';

        return $user;
    }

    public function validate() : void
    {
        if ( !filter_var( $this->email, FILTER_VALIDATE_EMAIL ) )
        {
            throw new \application\exceptions\ValidationException( 'Некорректный email' );
        }

        if ( empty( $this->password_hash ) )
        {
            throw new \application\exceptions\ValidationException( 'Пароль не может быть пустым' );
        }

        if ( empty( $this->name ) || mb_strlen( $this->name ) < 2 )
        {
            throw new \application\exceptions\ValidationException( 'Имя должно содержать не менее 2 символов' );
        }
    }

    public static function findByEmail( string $email ) : ?self
    {
        $row = self::db()->query( "SELECT * FROM `users` WHERE `email` = '?s' LIMIT 1", $email )->fetchAssoc();

        return $row ? self::createByRow( $row ) : null;
    }

    public static function findById( int $id ) : ?self
    {
        $row = self::db()->query( "SELECT * FROM `users` WHERE `id` = ?i LIMIT 1", $id )->fetchAssoc();

        return $row ? self::createByRow( $row ) : null;
    }

    public static function register( array $data ) : self
    {
        $user = self::create( $data );

        $user->password_hash = password_hash( $data['password'], PASSWORD_DEFAULT );

        $user->validate();

        if ( self::findByEmail( $user->email ) )
        {
            throw new \application\exceptions\ValidationException( 'Пользователя с таким email уже существует' );
        }

        self::db()->query( "INSERT INTO `users` (`email`, `password_hash`, `name`, `role`) VALUES ('?s', '?s', '?s', '?s')", $user->email, $user->password_hash, $user->name, $user->role );

        $user->id = self::db()->getLastInsertId();

        return $user;
    }

    public static function login( string $email, string $password ) : ?self
    {
        if ( !( $user = self::findByEmail( $email ) ) || !password_verify( $password, $user->password_hash ) )
        {
            return null;
        }

        return $user;
    }

    public static function isAuthorized() : bool
    {
        return isset( $_SESSION['user_id'] ) && $_SESSION['user_id'];
    }

    private static function createByRow( array $row ) : self
    {
        $user = new self;

        $user->id = (int)$row['id'];
        $user->email = $row['email'];
        $user->password_hash = $row['password_hash'];
        $user->name = $row['name'];
        $user->role = $row['role'];

        return $user;
    }

    public function toArray() : array
    {
        return [
            'id'    => $this->id,
            'email' => $this->email,
            'name'  => $this->name,
            'role'  => $this->role
        ];
    }

    public function getAvatarLetters() : string
    {
        if ( empty( $name = trim( $this->name ) ) )
        {
            return '';
        }

        if ( count( $words = preg_split( '/\s+/', $name ) ) === 1 )
        {
            return mb_strtoupper( mb_substr( $words[0], 0, 1 ) );
        }

        return mb_strtoupper( mb_substr( $words[0], 0, 1 ) ) . mb_strtoupper( mb_substr( $words[1], 0, 1 ) );
    }

}
