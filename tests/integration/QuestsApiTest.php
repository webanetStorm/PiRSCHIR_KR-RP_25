<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 03.12.2025
 * Time: 23:36
 */

namespace tests\integration;


class QuestsApiTest extends \PHPUnit\Framework\TestCase
{

    private string $_baseUrl = 'http://localhost:8080/api';

    private ?string $_token = null;

    private ?array $_user = null;

    private int $_testQuestId = 0;


    protected function setUp() : void
    {
        $this->registerTestUser();
    }

    private function registerTestUser() : void
    {
        $data = [
            'email'    => 'test_' . uniqid() . '@example.com',
            'password' => 'password123',
            'name'     => 'Test User'
        ];

        $response = $this->makeRequest( '/auth/register', 'POST', $data );

        if ( isset( $response['success'] ) && $response['success'] )
        {
            $this->_token = $response['data']['token'] ?? null;
            $this->_user = $response['data']['user'] ?? null;
        }
    }

    private function makeRequest( string $endpoint, string $method = 'GET', array $data = [] ) : array
    {
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $this->_baseUrl . $endpoint );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $method );

        $headers = [ 'Content-Type: application/json' ];

        if ( $this->_token )
        {
            $headers[] = 'Authorization: Bearer ' . $this->_token;
        }

        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $data ) );

        $result = curl_exec( $ch );

        curl_close( $ch );

        return json_decode( $result, true ) ?: [];
    }

    private function createTestQuest() : int
    {
        $data = [
            'title'       => 'Test Quest ' . uniqid(),
            'description' => 'Test quest description',
            'type'        => 'individual',
            'reward'      => 50
        ];

        return $this->makeRequest( '/quests/create', 'POST', $data )['data']['id'] ?? 0;
    }

    public function testRegisterSuccess() : void
    {
        $email = 'test_' . uniqid() . '@example.com';
        $data = [
            'email'    => $email,
            'password' => 'password123',
            'name'     => 'Test User ' . uniqid()
        ];

        $response = $this->makeRequest( '/auth/register', 'POST', $data );

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

        $response = $this->makeRequest( '/auth/register', 'POST', $data );

        $this->assertFalse( $response['success'] );
    }

    public function testRegisterShortPassword() : void
    {
        $data = [
            'email'    => 'test_' . uniqid() . '@example.com',
            'password' => '123',
            'name'     => 'Test User'
        ];

        $response = $this->makeRequest( '/auth/register', 'POST', $data );

        $this->assertFalse( $response['success'] );
        $this->assertEquals( 'Ошибка при регистрации: Пароль должен содержать не менее 4 символов', $response['message'] );
    }

    public function testRegisterDuplicateEmail() : void
    {
        $email = 'duplicate_' . uniqid() . '@example.com';
        $data = [
            'email'    => $email,
            'password' => 'password123',
            'name'     => 'Test User'
        ];

        $this->makeRequest( '/auth/register', 'POST', $data );

        $response = $this->makeRequest( '/auth/register', 'POST', $data );

        $this->assertFalse( $response['success'] );
    }

    public function testRegisterInvalidEmail() : void
    {
        $data = [
            'email'    => 'not-an-email',
            'password' => 'password123',
            'name'     => 'Test User'
        ];

        $response = $this->makeRequest( '/auth/register', 'POST', $data );

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

        $response = $this->makeRequest( '/auth/register', 'POST', $data );

        $this->assertFalse( $response['success'] );
    }

    public function testRegisterEmptyJson() : void
    {
        $data = [];

        $response = $this->makeRequest( '/auth/register', 'POST', $data );

        $this->assertFalse( $response['success'] );
    }

    public function testRegisterMissingFields() : void
    {
        $data = [
            'email' => 'test745@example.com'
        ];

        $response = $this->makeRequest( '/auth/register', 'POST', $data );

        $this->assertFalse( $response['success'] );
    }

    public function testRegisterSetsUserRole() : void
    {
        $data = [
            'email'    => 'role_' . uniqid() . '@example.com',
            'password' => 'password123',
            'name'     => 'Test User'
        ];

        $response = $this->makeRequest( '/auth/register', 'POST', $data );

        $this->assertTrue( $response['success'] );
        $this->assertEquals( 'user', $response['data']['user']['role'] );
    }

    public function testRegisterReturnsValidTokenFormat() : void
    {
        $data = [
            'email'    => 'token_' . uniqid() . '@example.com',
            'password' => 'password123',
            'name'     => 'Token Test'
        ];

        $response = $this->makeRequest( '/auth/register', 'POST', $data );

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
        $this->assertIsArray( $this->makeRequest( '/auth/register' ) );
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
        $this->makeRequest( '/auth/register', 'POST', $registerData );

        $loginData = [
            'email'    => $email,
            'password' => $password
        ];
        $response = $this->makeRequest( '/auth/login', 'POST', $loginData );

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
        $this->makeRequest( '/auth/register', 'POST', $registerData );

        $loginData = [
            'email'    => $email,
            'password' => 'wrongpassword'
        ];
        $response = $this->makeRequest( '/auth/login', 'POST', $loginData );

        $this->assertFalse( $response['success'] );
    }

    public function testLoginEmptyFields() : void
    {
        $data = [
            'email'    => '',
            'password' => ''
        ];

        $response = $this->makeRequest( '/auth/login', 'POST', $data );

        $this->assertFalse( $response['success'] );
    }

    public function testLoginNonExistentUser() : void
    {
        $data = [
            'email'    => 'nonexistent_' . uniqid() . '@example.com',
            'password' => 'password123'
        ];

        $response = $this->makeRequest( '/auth/login', 'POST', $data );

        $this->assertFalse( $response['success'] );
    }

    public function testListQuests() : void
    {
        $response = $this->makeRequest( '/quests' );

        $this->assertIsArray( $response );
        $this->assertArrayHasKey( 'success', $response );
        $this->assertTrue( $response['success'] );
        $this->assertArrayHasKey( 'data', $response );
        $this->assertIsArray( $response['data'] );
    }

    public function testCreateQuestSuccess() : void
    {
        $data = [
            'title'       => 'Test Quest ' . uniqid(),
            'description' => 'Test quest description',
            'type'        => 'individual',
            'reward'      => 50,
        ];

        $response = $this->makeRequest( '/quests/create', 'POST', $data );

        $this->assertTrue( $response['success'] );
        $this->assertEquals( 'Квест успешно создан', $response['message'] );
        $this->assertArrayHasKey( 'id', $response['data'] );

        $this->_testQuestId = $response['data']['id'];
    }

    public function testCreateQuestValidationError() : void
    {
        $data = [
            'title'       => 'AB',
            'description' => '',
            'type'        => 'invalid',
            'reward'      => 0
        ];

        $response = $this->makeRequest( '/quests/create', 'POST', $data );

        $this->assertFalse( $response['success'] );
    }

    public function testViewQuestSuccess() : void
    {
        $data = [
            'title'       => 'Test Quest ' . uniqid(),
            'description' => 'Test quest description',
            'type'        => 'individual',
            'reward'      => 50,
        ];

        $questId = $this->makeRequest( '/quests/create', 'POST', $data )['data']['id'];

        $this->makeRequest( "/quests/$questId/publish" );

        $response = $this->makeRequest( "/quests/$questId" );

        $this->assertTrue( $response['success'] );
        $this->assertEquals( $questId, $response['data']['id'] );
        $this->assertArrayHasKey( 'is_owner', $response['data'] );
        $this->assertTrue( $response['data']['is_owner'] );
    }

    public function testViewQuestNotFound() : void
    {
        $response = $this->makeRequest( '/quests/999999' );

        $this->assertFalse( $response['success'] );
        $this->assertEquals( 'Ресурс не найден', $response['message'] );
    }

    public function testMyQuests() : void
    {
        $this->testCreateQuestSuccess();

        $response = $this->makeRequest( '/quests/my' );

        $this->assertTrue( $response['success'] );
        $this->assertIsArray( $response['data'] );
        $this->assertGreaterThan( 0, count( $response['data'] ) );
    }

    public function testMyQuestsUnauthorized() : void
    {
        $this->_token = null;
        $response = $this->makeRequest( '/quests/my' );

        $this->assertFalse( $response['success'] );
        $this->assertEquals( 'Требуется авторизация', $response['message'] );
    }

    public function testUpdateQuestNotFound() : void
    {
        $response = $this->makeRequest( '/quests/999999/update', 'POST', [ 'title' => 'Test' ] );

        $this->assertFalse( $response['success'] );
        $this->assertEquals( 'Ресурс не найден', $response['message'] );
    }

    public function testUpdateQuestForbidden() : void
    {
        $this->testCreateQuestSuccess();

        $oldToken = $this->_token;
        $this->_token = 'invalid_token';

        $response = $this->makeRequest( "/quests/{$this->_testQuestId}/update", 'POST', [ 'title' => 'Test' ] );

        $this->_token = $oldToken;
        $this->assertFalse( $response['success'] );
        $this->assertEquals( 'Неверный или просроченный токен', $response['message'] );
    }

    public function testPublishQuestSuccess() : void
    {
        $this->testCreateQuestSuccess();

        $response = $this->makeRequest( "/quests/{$this->_testQuestId}/publish", 'POST' );

        $this->assertTrue( $response['success'] );
        $this->assertEquals( 'Квест успешно опубликован', $response['message'] );
        $this->assertEquals( 'active', $response['data']['status'] );
    }

    public function testPublishQuestNotFound() : void
    {
        $response = $this->makeRequest( '/quests/999999/publish', 'POST' );

        $this->assertFalse( $response['success'] );
        $this->assertEquals( 'Ресурс не найден', $response['message'] );
    }

    public function testPublishAlreadyPublished() : void
    {
        $this->testCreateQuestSuccess();
        $this->makeRequest( "/quests/$this->_testQuestId/publish", 'POST' );

        $response = $this->makeRequest( "/quests/$this->_testQuestId/publish", 'POST' );

        $this->assertFalse( $response['success'] );
        $this->assertEquals( 'Можно публиковать только черновики', $response['message'] );
    }

    public function testDeleteQuestNotFound() : void
    {
        $response = $this->makeRequest( '/quests/999999/delete', 'DELETE' );

        $this->assertFalse( $response['success'] );
        $this->assertEquals( 'Ресурс не найден', $response['message'] );
    }

    public function testDeleteQuestForbidden() : void
    {
        $this->testCreateQuestSuccess();

        $oldToken = $this->_token;
        $this->_token = null;

        $response = $this->makeRequest( "/quests/{$this->_testQuestId}/delete", 'DELETE' );

        $this->_token = $oldToken;
        $this->assertFalse( $response['success'] );
        $this->assertEquals( 'Требуется авторизация', $response['message'] );
    }

    public function testListQuestsUnauthenticated() : void
    {
        $this->_token = null;
        $response = $this->makeRequest( '/quests' );

        $this->assertFalse( $response['success'] );
    }

    public function testCreateQuestUnauthorized() : void
    {
        $this->_token = null;
        $response = $this->makeRequest( '/quests/create', 'POST', [
            'title'       => 'Test',
            'description' => 'Test'
        ] );

        $this->assertFalse( $response['success'] );
        $this->assertEquals( 'Требуется авторизация', $response['message'] );
    }

    public function testQuestTypeValidation() : void
    {
        $data = [
            'title'       => 'Test Quest',
            'description' => 'Description',
            'type'        => 'invalid_type'
        ];

        $response = $this->makeRequest( '/quests/create', 'POST', $data );

        $this->assertFalse( $response['success'] );
    }

    public function testCollectiveQuestValidation() : void
    {
        $data = [
            'title'            => 'Collective Quest',
            'description'      => 'Description',
            'type'             => 'collective',
            'min_participants' => 1
        ];

        $response = $this->makeRequest( '/quests/create', 'POST', $data );

        $this->assertFalse( $response['success'] );
    }

    public function testTimedQuestValidation() : void
    {
        $data = [
            'title'       => 'Timed Quest',
            'description' => 'Description',
            'type'        => 'timed',
            'deadline'    => '2020-01-01 00:00:00'
        ];

        $response = $this->makeRequest( '/quests/create', 'POST', $data );

        $this->assertFalse( $response['success'] );
    }

    public function testValidTimedQuest() : void
    {
        $data = [
            'title'       => 'Future Quest',
            'description' => 'Description',
            'type'        => 'timed',
            'deadline'    => date( 'Y-m-d H:i:s', strtotime( '+1 day' ) ),
            'reward'      => 100
        ];

        $response = $this->makeRequest( '/quests/create', 'POST', $data );

        $this->assertTrue( $response['success'] );
    }

    public function testRewardValidation() : void
    {
        $data = [
            'title'       => 'Low Reward',
            'description' => 'Description',
            'type'        => 'individual',
            'reward'      => 0
        ];

        $response = $this->makeRequest( '/quests/create', 'POST', $data );

        $this->assertFalse( $response['success'] );
    }

    public function testEmptyRequestValidation() : void
    {
        $response = $this->makeRequest( '/quests/create', 'POST', [] );

        $this->assertFalse( $response['success'] );
    }

    public function testProfileSuccess() : void
    {
        $response = $this->makeRequest( '/auth/profile' );

        $this->assertTrue( $response['success'] );
        $this->assertArrayHasKey( 'id', $response['data'] );
        $this->assertArrayHasKey( 'email', $response['data'] );
        $this->assertArrayHasKey( 'name', $response['data'] );
        $this->assertArrayHasKey( 'role', $response['data'] );
    }

    public function testProfileUnauthorized() : void
    {
        $this->_token = null;
        $response = $this->makeRequest( '/auth/profile' );

        $this->assertFalse( $response['success'] );
        $this->assertEquals( 'Требуется авторизация', $response['message'] );
    }

}
