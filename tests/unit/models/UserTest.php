<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 03.12.2025
 * Time: 12:02
 */

namespace tests\unit\models;


class UserTest extends \PHPUnit\Framework\TestCase
{

    public function testToArray()
    {
        $u = new \application\models\User();
        $u->id = 10;
        $u->email = 'a@b.c';
        $u->name = 'John Doe';
        $u->role = 'user';

        $this->assertSame( [
            'id'    => 10,
            'email' => 'a@b.c',
            'name'  => 'John Doe',
            'role'  => 'user'
        ], $u->toArray() );
    }

    public function testValidateSuccess()
    {
        $u = new \application\models\User();
        $u->email = 'test@mail.com';
        $u->password_hash = 'hash';
        $u->name = 'John';
        $u->role = 'admin';
        $u->validate();
        $this->assertTrue( true );
    }

    public function testValidateInvalidEmail()
    {
        $this->expectException( \application\exceptions\ValidationException::class );
        $u = new \application\models\User();
        $u->email = 'wrongemail';
        $u->password_hash = 'hash';
        $u->name = 'John';
        $u->role = 'user';
        $u->validate();
    }

    public function testValidateEmptyPassword()
    {
        $this->expectException( \application\exceptions\ValidationException::class );
        $u = new \application\models\User();
        $u->email = 'a@b.c';
        $u->password_hash = '';
        $u->name = 'John';
        $u->role = 'user';
        $u->validate();
    }

    public function testValidateNameTooShort()
    {
        $this->expectException( \application\exceptions\ValidationException::class );
        $u = new \application\models\User();
        $u->email = 'a@b.c';
        $u->password_hash = 'hash';
        $u->name = 'J';
        $u->role = 'user';
        $u->validate();
    }

    public function testValidateInvalidRole()
    {
        $this->expectException( \application\exceptions\ValidationException::class );
        $u = new \application\models\User();
        $u->email = 'a@b.c';
        $u->password_hash = 'hash';
        $u->name = 'John';
        $u->role = 'wrong';
        $u->validate();
    }

    public function testGetAvatarLettersSingle()
    {
        $u = new \application\models\User();
        $u->name = 'John';
        $this->assertSame( 'J', $u->getAvatarLetters() );
    }

    public function testGetAvatarLettersDouble()
    {
        $u = new \application\models\User();
        $u->name = 'John Doe';
        $this->assertSame( 'JD', $u->getAvatarLetters() );
    }

    public function testGetAvatarLettersTripled()
    {
        $u = new \application\models\User();
        $u->name = 'A VB C';
        $this->assertSame( 'AV', $u->getAvatarLetters() );
    }

    public function testGetAvatarLettersEmpty()
    {
        $u = new \application\models\User();
        $u->name = '';
        $this->assertSame( '', $u->getAvatarLetters() );
    }

    public function testGetAvatarLettersExtraSpaces()
    {
        $u = new \application\models\User();
        $u->name = '  John   Doe   ';
        $this->assertSame( 'JD', $u->getAvatarLetters() );
    }

}
