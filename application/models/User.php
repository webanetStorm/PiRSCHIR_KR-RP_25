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

    public int $id = 0;

    public string $email = '';

    public string $password_hash = '';

    public string $name = '';

    public string $role = '';


    public function toArray() : array
    {
        return [
            'id'    => $this->id,
            'email' => $this->email,
            'name'  => $this->name,
            'role'  => $this->role
        ];
    }

    /**
     * @throws \application\exceptions\ValidationException
     */
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

        if ( !in_array( $this->role, [ 'guest', 'user', 'admin' ] ) )
        {
            throw new \application\exceptions\ValidationException( 'Некорректная роль' );
        }
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
