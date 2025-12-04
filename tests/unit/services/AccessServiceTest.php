<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 04.12.2025
 * Time: 22:21
 */

namespace tests\unit\services;


class AccessServiceTest extends \PHPUnit\Framework\TestCase
{

    public function testGuestCanAccessGuestResource() : void
    {
        $service = new \application\services\AccessService( 'guest' );
        $service->check( 'main', 'index' );
        $this->assertTrue( true );
    }

    public function testGuestCannotAccessUserResource() : void
    {
        $service = new \application\services\AccessService( 'guest' );
        $this->expectException( \application\exceptions\ForbiddenException::class );
        $service->check( 'quests', 'create' );
    }

    public function testUserCanAccessUserResource() : void
    {
        $service = new \application\services\AccessService( 'user' );
        $service->check( 'quests', 'create' );
        $this->assertTrue( true );
    }

    public function testUnknownRuleThrowsDomainException() : void
    {
        $service = new \application\services\AccessService( 'user' );
        $this->expectException( \application\exceptions\DomainException::class );
        $service->check( 'unknown_controller', 'unknown_action' );
    }

    public function testIsGuest() : void
    {
        $service = new \application\services\AccessService( 'guest' );
        $this->assertTrue( $service->isGuest() );
        $this->assertFalse( $service->isUser() );
        $this->assertFalse( $service->isAdmin() );
    }

    public function testIsUser() : void
    {
        $service = new \application\services\AccessService( 'user' );
        $this->assertTrue( $service->isUser() );
        $this->assertFalse( $service->isGuest() );
        $this->assertFalse( $service->isAdmin() );
    }

    public function testIsAdmin() : void
    {
        $service = new \application\services\AccessService( 'admin' );
        $this->assertTrue( $service->isAdmin() );
        $this->assertTrue( $service->isUser() );
        $this->assertFalse( $service->isGuest() );
    }

}
