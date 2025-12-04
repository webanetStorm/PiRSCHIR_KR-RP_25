<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 04.12.2025
 * Time: 23:47
 */

namespace tests\integration;


class AdminApiTest extends \PHPUnit\Framework\TestCase
{

    private string $_baseUrl = 'http://localhost:8080';

    private string $_token = '';


    protected function setUp() : void
    {
        $this->loginAsAdmin();
    }

    private function loginAsAdmin() : void
    {
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $this->_baseUrl . '/api/auth/login' );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/json' ] );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( [
            'email'    => 'admin@quelyd.local',
            'password' => 'root'
        ] ) );

        $result = curl_exec( $ch );
        curl_close( $ch );

        $response = json_decode( $result, true );
        $this->_token = $response['data']['token'] ?? '';
    }

    private function makeRequest( string $endpoint, string $method = 'GET', array $data = [] ) : array
    {
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $this->_baseUrl . '/api' . $endpoint );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $method );

        $headers = [ 'Content-Type: application/json' ];

        if ( $this->_token )
        {
            $headers[] = 'Authorization: Bearer ' . $this->_token;
        }

        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

        if ( !empty( $data ) && in_array( strtoupper( $method ), [ 'POST', 'PUT', 'PATCH', 'DELETE' ] ) )
        {
            curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $data ) );
        }

        $result = curl_exec( $ch );
        curl_close( $ch );

        return json_decode( $result, true ) ?: [];
    }

    public function testListPendingReturnsUnapprovedQuests() : void
    {
        $response = $this->makeRequest( '/admin' );

        $this->assertArrayHasKey( 'success', $response );
        $this->assertTrue( $response['success'] );
        $this->assertArrayHasKey( 'data', $response );
        $this->assertIsArray( $response['data'] );

        foreach ( $response['data'] as $quest )
        {
            $this->assertArrayHasKey( 'is_approved', $quest );
            $this->assertFalse( $quest['is_approved'] );
        }
    }

    public function testApproveQuestChangesIsApproved() : void
    {
        $response = $this->makeRequest( "/admin/approve/{$this->createQuest()}", 'POST' );

        $this->assertArrayHasKey( 'success', $response );
        $this->assertTrue( $response['success'] );
        $this->assertEquals( 'Квест одобрен', $response['message'] );
    }

    public function testRejectQuestDeletesIt() : void
    {
        $response = $this->makeRequest( "/admin/reject/{$this->createQuest()}", 'DELETE' );

        $this->assertArrayHasKey( 'success', $response );
        $this->assertTrue( $response['success'] );
        $this->assertEquals( 'Квест удалён', $response['message'] );
    }

    private function createQuest() : int
    {
        return $this->makeRequest( '/quests/create', 'POST', [
            "title"            => "Поиск древнего артефакта",
            "description"      => "Найти древний артефакт в руинах старого храма",
            "type"             => "collective",
            "reward"           => 200,
            "min_participants" => 3,
            "deadline"         => "2025-12-31 23:59:59"
        ] )['data']['id'];
    }

}
