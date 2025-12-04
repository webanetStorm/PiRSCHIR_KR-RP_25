<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 04.12.2025
 * Time: 22:25
 */

namespace tests\unit\services;


class UserServiceTest extends \PHPUnit\Framework\TestCase
{

    private \application\services\UserService $_userService;

    private \application\repositories\UserRepository $_mockRepo;


    protected function setUp() : void
    {
        $this->_mockRepo = $this->createMock( \application\repositories\UserRepository::class );
        $this->_userService = new \application\services\UserService( $this->_mockRepo );
    }

    public function testRegisterCreatesValidUser() : void
    {
        $userData = [
            'email'    => 'newuser@example.com',
            'password' => 'password123',
            'name'     => 'New User'
        ];

        $this->_mockRepo->expects( $this->once() )
            ->method( 'findByEmail' )
            ->with( 'newuser@example.com' )
            ->willReturn( null );

        $this->_mockRepo->expects( $this->once() )->method( 'save' );

        $user = $this->_userService->register( $userData );

        $this->assertEquals( 'newuser@example.com', $user->email );
        $this->assertEquals( 'New User', $user->name );
        $this->assertEquals( 'user', $user->role );
        $this->assertNotEmpty( $user->password_hash );
    }

    public function testRegisterThrowsExceptionForDuplicateEmail() : void
    {
        $userData = [
            'email'    => 'existing@example.com',
            'password' => 'password123',
            'name'     => 'User'
        ];

        $existingUser = new \application\models\User( [
            'id'            => 1,
            'email'         => 'existing@example.com',
            'password_hash' => 'hash',
            'name'          => 'Existing',
            'role'          => 'user'
        ] );

        $this->_mockRepo->expects( $this->once() )
            ->method( 'findByEmail' )
            ->with( 'existing@example.com' )
            ->willReturn( $existingUser );

        $this->expectException( \application\exceptions\ValidationException::class );
        $this->expectExceptionMessage( 'Пользователь с таким email уже существует' );

        $this->_userService->register( $userData );
    }

    public function testRegisterThrowsExceptionForShortPassword() : void
    {
        $userData = [
            'email'    => 'newuser@example.com',
            'password' => '123',
            'name'     => 'User'
        ];

        $this->expectException( \application\exceptions\ValidationException::class );
        $this->expectExceptionMessage( 'Пароль должен содержать не менее 4 символов' );

        $this->_userService->register( $userData );
    }

    public function testLoginSuccess() : void
    {
        $email = 'user@example.com';
        $password = 'password123';
        $hashedPassword = password_hash( $password, PASSWORD_DEFAULT );

        $user = new \application\models\User( [
            'id'            => 1,
            'email'         => $email,
            'password_hash' => $hashedPassword,
            'name'          => 'Test User',
            'role'          => 'user'
        ] );

        $this->_mockRepo->expects( $this->once() )->method( 'findByEmail' )->with( $email )->willReturn( $user );

        $this->_userService->login( $email, $password );

        $this->assertEquals( $user->id, $_SESSION['user_id'] );
    }

    public function testLoginFailureWrongPassword() : void
    {
        $email = 'user@example.com';
        $password = 'wrongpassword';
        $correctHashedPassword = password_hash( 'correctpassword', PASSWORD_DEFAULT );

        $user = new \application\models\User( [
            'id'            => 1,
            'email'         => $email,
            'password_hash' => $correctHashedPassword,
            'name'          => 'Test User',
            'role'          => 'user'
        ] );

        $this->_mockRepo->expects( $this->once() )->method( 'findByEmail' )->with( $email )->willReturn( $user );

        $this->expectException( \application\exceptions\ValidationException::class );
        $this->expectExceptionMessage( 'Неверный email или пароль' );

        $this->_userService->login( $email, $password );
    }

    public function testLoginFailureNonExistentUser() : void
    {
        $email = 'nonexistent@example.com';
        $password = 'password123';

        $this->_mockRepo->expects( $this->once() )->method( 'findByEmail' )->with( $email )->willReturn( null );

        $this->expectException( \application\exceptions\ValidationException::class );
        $this->expectExceptionMessage( 'Неверный email или пароль' );

        $this->_userService->login( $email, $password );
    }

    public function testLogoutClearsSession() : void
    {
        $_SESSION['user_id'] = 123;

        $this->_userService->logout();

        $this->assertArrayNotHasKey( 'user_id', $_SESSION );
    }

    public function testGetCurrentUserReturnsUserWhenLoggedIn() : void
    {
        $_SESSION['user_id'] = 123;

        $expectedUser = new \application\models\User( [
            'id'            => 123,
            'email'         => 'test@example.com',
            'name'          => 'Test User',
            'role'          => 'user',
            'password_hash' => 'hash'
        ] );

        $this->_mockRepo->expects( $this->once() )->method( 'findById' )->with( 123 )->willReturn( $expectedUser );

        $user = $this->_userService->getCurrentUser();

        $this->assertInstanceOf( \application\models\User::class, $user );
        $this->assertEquals( $expectedUser->id, $user->id );
    }

    public function testGetCurrentUserReturnsNullWhenNotLoggedIn() : void
    {
        unset( $_SESSION['user_id'] );

        $user = $this->_userService->getCurrentUser();

        $this->assertNull( $user );
    }

    public function testIsLoggedInReturnsTrue() : void
    {
        $_SESSION['user_id'] = 123;

        $this->assertTrue( \application\services\UserService::isLoggedIn() );
    }

    public function testIsLoggedInReturnsFalse() : void
    {
        unset( $_SESSION['user_id'] );

        $this->assertFalse( \application\services\UserService::isLoggedIn() );
    }

}
