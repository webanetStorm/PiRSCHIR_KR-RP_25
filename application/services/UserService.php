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

    use \application\core\InstanceTrait;


    private \application\repositories\UserRepository $_userRepository;


    public function __construct( \application\repositories\UserRepository $userRepository )
    {
        $this->_userRepository = $userRepository;
    }

    /**
     * @throws \Krugozor\Database\MySqlException
     * @throws \application\exceptions\ValidationException
     */
    public function register( array $data ) : \application\models\User
    {
        $user = new \application\models\User( array_merge( $data, [ 'password_hash' => password_hash( $data['password'], PASSWORD_DEFAULT ), 'role' => 'user' ] ) );

        if ( $this->_userRepository->findByEmail( $user->email ) )
        {
            throw new \application\exceptions\ValidationException( 'Пользователь с таким email уже существует' );
        }

        if ( strlen( $data['password'] ) < 4 )
        {
            throw new \application\exceptions\ValidationException( 'Пароль должен содержать не менее 4 символов' );
        }

        $user->validate();

        $this->_userRepository->save( $user );

        return $user;
    }

    /**
     * @throws \Krugozor\Database\MySqlException
     * @throws \application\exceptions\ValidationException
     */
    public function login( string $email, string $password ) : \application\models\User
    {
        if ( !( $user = $this->_userRepository->findByEmail( $email ) ) || !password_verify( $password, $user->password_hash ) )
        {
            throw new \application\exceptions\ValidationException( 'Неверный email или пароль' );
        }

        $_SESSION['user_id'] = $user->id;

        return $user;
    }

    public function logout() : void
    {
        unset( $_SESSION['user_id'] );
    }

    /**
     * @throws \Krugozor\Database\MySqlException
     */
    public function getCurrentUser() : ?\application\models\User
    {
        if ( self::isLoggedIn() )
        {
            return $this->_userRepository->findById( $_SESSION['user_id'] );
        }

        return null;
    }

    public static function isLoggedIn() : bool
    {
        return isset( $_SESSION['user_id'] ) && $_SESSION['user_id'];
    }

}
