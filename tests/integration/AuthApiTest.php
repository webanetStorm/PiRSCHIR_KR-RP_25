<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 04.12.2025
 * Time: 0:20
 */

namespace tests\integration;


class AuthApiTest extends \PHPUnit\Framework\TestCase
{

    private string $_baseUrl = 'http://localhost:8080/api/auth';


    private function makeRequest( string $endpoint, string $method = 'GET', array $data = [] ) : array
    {
        $url = $this->_baseUrl . $endpoint;
        $options = [
            'http' => [
                'method'        => $method,
                'header'        => "Content-Type: application/json\r\n",
                'ignore_errors' => true
            ]
        ];

        if ( !empty( $data ) && in_array( $method, [ 'POST', 'PUT', 'PATCH' ] ) )
        {
            $options['http']['content'] = json_encode( $data );
        }

        $context = stream_context_create( $options );
        $response = file_get_contents( $url, false, $context );

        return json_decode( $response, true ) ?: [];
    }

    public function testRegisterSuccess() : void
    {
        $email = 'test_' . uniqid() . '@example.com';
        $data = [
            'email'    => $email,
            'password' => 'password123',
            'name'     => 'Test User ' . uniqid()
        ];

        $response = $this->makeRequest( '/register', 'POST', $data );

        $this->assertArrayHasKey( 'success', $response );
        $this->assertTrue( $response['success'] );
        $this->assertEquals( 'Успешная регистрация', $response['message'] );
        $this->assertArrayHasKey( 'data', $response );
        $this->assertArrayHasKey( 'token', $response['data'] );
        $this->assertArrayHasKey( 'user', $response['data'] );
        $this->assertEquals( $email, $response['data']['user']['email'] );
        $this->assertNotEmpty( $response['data']['token'] );
    }

    public function testRegisterEmptyFields() : void
    {
        $data = [
            'email'    => '',
            'password' => '',
            'name'     => ''
        ];

        $response = $this->makeRequest( '/register', 'POST', $data );

        $this->assertFalse( $response['success'] );
    }

    public function testRegisterShortPassword() : void
    {
        $data = [
            'email'    => 'test345@example.com',
            'password' => '123',
            'name'     => 'Test User'
        ];

        $response = $this->makeRequest( '/register', 'POST', $data );

        $this->assertFalse( $response['success'] );
    }

    public function testRegisterDuplicateEmail() : void
    {
        $email = 'duplicate_' . uniqid() . '@example.com';
        $data = [
            'email'    => $email,
            'password' => 'password123',
            'name'     => 'Test User'
        ];

        $this->makeRequest( '/register', 'POST', $data );

        $response = $this->makeRequest( '/register', 'POST', $data );

        $this->assertFalse( $response['success'] );
    }

    public function testRegisterInvalidEmail() : void
    {
        $data = [
            'email'    => 'not-an-email',
            'password' => 'password123',
            'name'     => 'Test User'
        ];

        $response = $this->makeRequest( '/register', 'POST', $data );

        $this->assertFalse( $response['success'] );
        $this->assertStringContainsString( 'email', $response['message'] );
    }

    public function testRegisterShortName() : void
    {
        $data = [
            'email'    => 'test@example.com',
            'password' => 'password123',
            'name'     => 'A'
        ];

        $response = $this->makeRequest( '/register', 'POST', $data );

        $this->assertFalse( $response['success'] );
    }

    public function testRegisterEmptyJson() : void
    {
        $data = [];

        $response = $this->makeRequest( '/register', 'POST', $data );

        $this->assertFalse( $response['success'] );
    }

    public function testRegisterMissingFields() : void
    {
        $data = [
            'email' => 'test234@example.com'
        ];

        $response = $this->makeRequest( '/register', 'POST', $data );

        $this->assertFalse( $response['success'] );
    }

    public function testRegisterSetsUserRole() : void
    {
        $email = 'role_' . uniqid() . '@example.com';
        $data = [
            'email'    => $email,
            'password' => 'password123',
            'name'     => 'Test User'
        ];

        $response = $this->makeRequest( '/register', 'POST', $data );

        $this->assertTrue( $response['success'] );
        $this->assertEquals( 'user', $response['data']['user']['role'] );
    }

    public function testRegisterReturnsValidTokenFormat() : void
    {
        $email = 'token_' . uniqid() . '@example.com';
        $data = [
            'email'    => $email,
            'password' => 'password123',
            'name'     => 'Token Test'
        ];

        $response = $this->makeRequest( '/register', 'POST', $data );

        $this->assertTrue( $response['success'] );
        $token = $response['data']['token'];
        $decoded = base64_decode( $token, true );
        $this->assertNotFalse( $decoded );
        $tokenData = json_decode( $decoded, true );
        $this->assertIsArray( $tokenData );
        $this->assertArrayHasKey( 'user_id', $tokenData );
        $this->assertArrayHasKey( 'email', $tokenData );
        $this->assertArrayHasKey( 'role', $tokenData );
        $this->assertArrayHasKey( 'exp', $tokenData );
    }

    public function testRegisterWrongMethod() : void
    {
        $response = $this->makeRequest( '/register' );

        $this->assertIsArray( $response );
    }

    public function testLoginSuccess() : void
    {
        $email = 'login_' . uniqid() . '@example.com';
        $password = 'password123';

        $registerData = [
            'email'    => $email,
            'password' => $password,
            'name'     => 'Login Test User'
        ];
        $this->makeRequest( '/register', 'POST', $registerData );

        $loginData = [
            'email'    => $email,
            'password' => $password
        ];
        $response = $this->makeRequest( '/login', 'POST', $loginData );

        $this->assertTrue( $response['success'] );
        $this->assertEquals( 'Успешная авторизация', $response['message'] );
        $this->assertArrayHasKey( 'token', $response['data'] );
        $this->assertArrayHasKey( 'user', $response['data'] );
        $this->assertEquals( $email, $response['data']['user']['email'] );
    }

    public function testLoginWrongPassword() : void
    {
        $email = 'login_wrong_' . uniqid() . '@example.com';
        $registerData = [
            'email'    => $email,
            'password' => 'password123',
            'name'     => 'Login Test User'
        ];
        $this->makeRequest( '/register', 'POST', $registerData );

        $loginData = [
            'email'    => $email,
            'password' => 'wrongpassword'
        ];
        $response = $this->makeRequest( '/login', 'POST', $loginData );

        $this->assertFalse( $response['success'] );
    }

    public function testLoginEmptyFields() : void
    {
        $data = [
            'email'    => '',
            'password' => ''
        ];

        $response = $this->makeRequest( '/login', 'POST', $data );

        $this->assertFalse( $response['success'] );
    }

    public function testLoginNonExistentUser() : void
    {
        $data = [
            'email'    => 'nonexistent_' . uniqid() . '@example.com',
            'password' => 'password123'
        ];

        $response = $this->makeRequest( '/login', 'POST', $data );

        $this->assertFalse( $response['success'] );
    }

    public function testProfileSuccess() : void
    {
        $email = 'profile_' . uniqid() . '@example.com';
        $registerData = [
            'email'    => $email,
            'password' => 'password123',
            'name'     => 'Profile Test User'
        ];
        $registerResponse = $this->makeRequest( '/register', 'POST', $registerData );

        $token = $registerResponse['data']['token'] ?? null;

        $url = $this->_baseUrl . '/profile';
        $options = [
            'http' => [
                'method'        => 'GET',
                'header'        => "Content-Type: application/json\r\nAuthorization: Bearer $token\r\n",
                'ignore_errors' => true
            ]
        ];

        $context = stream_context_create( $options );
        $response = file_get_contents( $url, false, $context );
        $result = json_decode( $response, true );

        $this->assertTrue( $result['success'] );
        $this->assertArrayHasKey( 'id', $result['data'] );
        $this->assertArrayHasKey( 'email', $result['data'] );
        $this->assertEquals( $email, $result['data']['email'] );
        $this->assertArrayHasKey( 'name', $result['data'] );
        $this->assertArrayHasKey( 'role', $result['data'] );
    }

    public function testProfileUnauthorized() : void
    {
        $response = $this->makeRequest( '/profile' );

        $this->assertFalse( $response['success'] );
        $this->assertEquals( 'Требуется авторизация', $response['message'] );
    }

}
