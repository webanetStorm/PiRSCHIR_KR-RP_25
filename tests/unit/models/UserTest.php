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

    private \PHPUnit\Framework\MockObject\MockObject $_mockDb;

    private \PHPUnit\Framework\MockObject\MockObject $_mockStmt;


    protected function setUp() : void
    {
        $this->_mockDb = $this->createMock( \Krugozor\Database\Mysql::class );
        $this->_mockStmt = $this->createMock( \Krugozor\Database\Statement::class );

        $reflectionClass = new \ReflectionClass( \application\core\Model::class );
        $reflectionProperty = $reflectionClass->getProperty( '_connection' );
        $reflectionProperty->setValue( null, $this->_mockDb );
    }

    private function mockSelectResult( ?array $row ) : void
    {
        $this->_mockDb->method( 'query' )->willReturn( $this->_mockStmt );
        $this->_mockStmt->method( 'fetchAssoc' )->willReturn( $row );
    }

    private function mockInsertResult( int $insertId ) : void
    {
        $this->_mockDb->method( 'query' )->willReturn( $this->_mockStmt );
        $this->_mockDb->method( 'getLastInsertId' )->willReturn( $insertId );
    }

    public function testCreate() : void
    {
        $user = \application\models\User::create( [
            'email'         => 'test@example.com',
            'password_hash' => 'hashed_password_123',
            'name'          => 'John Doe',
            'role'          => 'admin'
        ] );

        $this->assertSame( 'test@example.com', $user->email );
        $this->assertSame( 'hashed_password_123', $user->password_hash );
        $this->assertSame( 'John Doe', $user->name );
        $this->assertSame( 'admin', $user->role );
    }

    public function testValidateWithValidData() : void
    {
        $user = \application\models\User::create( [
            'email'         => 'valid@example.com',
            'password_hash' => 'valid_hash',
            'name'          => 'Valid Name',
            'role'          => 'user'
        ] );

        $this->expectNotToPerformAssertions();
        $user->validate();
    }

    public function testValidateWithInvalidEmail() : void
    {
        $user = \application\models\User::create( [
            'email'         => 'invalid-email',
            'password_hash' => 'hash',
            'name'          => 'Test User',
            'role'          => 'user'
        ] );

        $this->expectException( \application\exceptions\ValidationException::class );
        $user->validate();
    }

    public function testValidateWithEmptyPassword() : void
    {
        $user = \application\models\User::create( [
            'email'         => 'test@example.com',
            'password_hash' => '',
            'name'          => 'Test User',
            'role'          => 'user'
        ] );

        $this->expectException( \application\exceptions\ValidationException::class );
        $user->validate();
    }

    public function testValidateWithShortName() : void
    {
        $user = \application\models\User::create( [
            'email'         => 'test@example.com',
            'password_hash' => 'hash',
            'name'          => 'A',
            'role'          => 'user'
        ] );

        $this->expectException( \application\exceptions\ValidationException::class );
        $user->validate();
    }

    public function testValidateWithEmptyName() : void
    {
        $user = \application\models\User::create( [
            'email'         => 'test@example.com',
            'password_hash' => 'hash',
            'name'          => '',
            'role'          => 'user'
        ] );

        $this->expectException( \application\exceptions\ValidationException::class );
        $user->validate();
    }

    public function testFindByEmailFound() : void
    {
        $this->mockSelectResult( [
            'id'            => 5,
            'email'         => 'existing@example.com',
            'password_hash' => 'hashed_password',
            'name'          => 'Existing User',
            'role'          => 'user'
        ] );

        $user = \application\models\User::findByEmail( 'existing@example.com' );

        $this->assertInstanceOf( \application\models\User::class, $user );
        $this->assertSame( 5, $user->id );
        $this->assertSame( 'existing@example.com', $user->email );
        $this->assertSame( 'Existing User', $user->name );
        $this->assertSame( 'user', $user->role );
        $this->assertSame( 'hashed_password', $user->password_hash );
    }

    public function testFindByEmailNotFound() : void
    {
        $this->mockSelectResult( null );

        $user = \application\models\User::findByEmail( 'nonexistent@example.com' );

        $this->assertNull( $user );
    }

    public function testFindByIdFound() : void
    {
        $this->mockSelectResult( [
            'id'            => 10,
            'email'         => 'user10@example.com',
            'password_hash' => 'hash10',
            'name'          => 'User Ten',
            'role'          => 'admin'
        ] );

        $user = \application\models\User::findById( 10 );

        $this->assertInstanceOf( \application\models\User::class, $user );
        $this->assertSame( 10, $user->id );
        $this->assertSame( 'user10@example.com', $user->email );
        $this->assertSame( 'User Ten', $user->name );
        $this->assertSame( 'admin', $user->role );
        $this->assertSame( 'hash10', $user->password_hash );
    }

    public function testFindByIdNotFound() : void
    {
        $this->mockSelectResult( null );

        $user = \application\models\User::findById( 999 );

        $this->assertNull( $user );
    }

    public function testRegisterSuccess() : void
    {
        $this->mockSelectResult( null );
        $this->mockInsertResult( 25 );

        $user = \application\models\User::register( [
            'email'    => 'newuser@example.com',
            'password' => 'secure_password_123',
            'name'     => 'New User',
            'role'     => 'user'
        ] );

        $this->assertInstanceOf( \application\models\User::class, $user );
        $this->assertSame( 25, $user->id );
        $this->assertSame( 'newuser@example.com', $user->email );
        $this->assertSame( 'New User', $user->name );
        $this->assertSame( 'user', $user->role );
        $this->assertTrue( password_verify( 'secure_password_123', $user->password_hash ) );
    }

    public function testRegisterUserAlreadyExists() : void
    {
        $existingUser = [
            'id'            => 1,
            'email'         => 'existing@example.com',
            'password_hash' => 'hash',
            'name'          => 'Existing',
            'role'          => 'user'
        ];

        $this->mockSelectResult( $existingUser );

        $this->expectException( \application\exceptions\ValidationException::class );

        \application\models\User::register( [
            'email'    => 'existing@example.com',
            'password' => 'password123',
            'name'     => 'New Name'
        ] );
    }

    public function testRegisterWithInvalidData() : void
    {
        $this->mockSelectResult( null );

        $this->expectException( \application\exceptions\ValidationException::class );

        \application\models\User::register( [
            'email'    => 'invalid-email',
            'password' => 'password123',
            'name'     => 'Test User'
        ] );
    }

    public function testLoginSuccess() : void
    {
        $password = 'correct_password';
        $hashedPassword = password_hash( $password, PASSWORD_DEFAULT );

        $this->mockSelectResult( [
            'id'            => 7,
            'email'         => 'login@example.com',
            'password_hash' => $hashedPassword,
            'name'          => 'Login User',
            'role'          => 'user'
        ] );

        $user = \application\models\User::login( 'login@example.com', $password );

        $this->assertInstanceOf( \application\models\User::class, $user );
        $this->assertSame( 7, $user->id );
        $this->assertSame( 'login@example.com', $user->email );
        $this->assertSame( 'Login User', $user->name );
    }

    public function testLoginWrongPassword() : void
    {
        $correctPassword = 'correct_password';
        $wrongPassword = 'wrong_password';
        $hashedPassword = password_hash( $correctPassword, PASSWORD_DEFAULT );

        $this->mockSelectResult( [
            'id'            => 8,
            'email'         => 'user@example.com',
            'password_hash' => $hashedPassword,
            'name'          => 'Test User',
            'role'          => 'user'
        ] );

        $user = \application\models\User::login( 'user@example.com', $wrongPassword );

        $this->assertNull( $user );
    }

    public function testLoginUserNotFound() : void
    {
        $this->mockSelectResult( null );

        $user = \application\models\User::login( 'nonexistent@example.com', 'password' );

        $this->assertNull( $user );
    }

    public function testToArray() : void
    {
        $user = new \application\models\User();
        $user->id = 42;
        $user->email = 'array@example.com';
        $user->name = 'Array Test';
        $user->role = 'admin';
        $user->password_hash = 'hash_not_included';

        $expected = [
            'id'    => 42,
            'email' => 'array@example.com',
            'name'  => 'Array Test',
            'role'  => 'admin'
        ];

        $this->assertSame( $expected, $user->toArray() );
        $this->assertArrayNotHasKey( 'password_hash', $user->toArray() );
    }

    public function testGetAvatarLettersSingleWord() : void
    {
        $user = new \application\models\User();
        $user->name = 'Alexander';

        $this->assertSame( 'A', $user->getAvatarLetters() );
    }

    public function testGetAvatarLettersTwoWords() : void
    {
        $user = new \application\models\User();
        $user->name = 'John Smith';

        $this->assertSame( 'JS', $user->getAvatarLetters() );
    }

    public function testGetAvatarLettersMultipleWords() : void
    {
        $user = new \application\models\User();
        $user->name = 'John Michael Smith';

        $this->assertSame( 'JM', $user->getAvatarLetters() );
    }

    public function testGetAvatarLettersWithExtraSpaces() : void
    {
        $user = new \application\models\User();
        $user->name = '  John   Smith  ';

        $this->assertSame( 'JS', $user->getAvatarLetters() );
    }

    public function testGetAvatarLettersEmptyName() : void
    {
        $user = new \application\models\User();
        $user->name = '';

        $this->assertSame( '', $user->getAvatarLetters() );
    }

    public function testGetAvatarLettersOnlySpaces() : void
    {
        $user = new \application\models\User();
        $user->name = '   ';

        $this->assertSame( '', $user->getAvatarLetters() );
    }

    public function testGetAvatarLettersWithUnicode() : void
    {
        $user = new \application\models\User();
        $user->name = 'Александр Пушкин';

        $this->assertSame( 'АП', $user->getAvatarLetters() );
    }

    public function testGetAvatarLettersLowercase() : void
    {
        $user = new \application\models\User();
        $user->name = 'john smith';

        $this->assertSame( 'JS', $user->getAvatarLetters() );
    }

    public function testGetAvatarLettersSingleCharacter() : void
    {
        $user = new \application\models\User();
        $user->name = 'A';

        $this->assertSame( 'A', $user->getAvatarLetters() );
    }

    public function testGetAvatarLettersTwoCharacters() : void
    {
        $user = new \application\models\User();
        $user->name = 'AB';

        $this->assertSame( 'A', $user->getAvatarLetters() );
    }

    public function testValidateMethodExists() : void
    {
        $user = new \application\models\User();

        $this->assertTrue( method_exists( $user, 'validate' ) );

        $reflection = new \ReflectionMethod( \application\models\User::class, 'validate' );
        $this->assertTrue( $reflection->isPublic() );
    }

    public function testCreateWithMinimumData() : void
    {
        $user = \application\models\User::create( [
            'email'         => 'min@example.com',
            'password_hash' => 'min_hash'
        ] );

        $this->assertSame( 'min@example.com', $user->email );
        $this->assertSame( 'min_hash', $user->password_hash );
        $this->assertSame( '', $user->name );
        $this->assertSame( 'user', $user->role );
    }

    public function testCreateWithCustomRole() : void
    {
        $user = \application\models\User::create( [
            'email'         => 'admin@example.com',
            'password_hash' => 'admin_hash',
            'name'          => 'Admin',
            'role'          => 'admin'
        ] );

        $this->assertSame( 'admin', $user->role );
    }

    public function testRegisterWithDifferentRoles() : void
    {
        $this->mockSelectResult( null );
        $this->mockInsertResult( 100 );

        $user1 = \application\models\User::register( [
            'email'    => 'user1@example.com',
            'password' => 'pass123',
            'name'     => 'User One'
        ] );
        $this->assertSame( 'user', $user1->role );

        $this->mockSelectResult( null );
        $this->mockInsertResult( 101 );

        $user2 = \application\models\User::register( [
            'email'    => 'user2@example.com',
            'password' => 'pass123',
            'name'     => 'User Two',
            'role'     => 'admin'
        ] );
        $this->assertSame( 'admin', $user2->role );
    }

    public function testValidationExceptionMessages() : void
    {
        $user1 = \application\models\User::create( [
            'email'         => 'bad-email',
            'password_hash' => 'hash',
            'name'          => 'Valid Name'
        ] );

        try
        {
            $user1->validate();
            $this->fail( 'Expected ValidationException was not thrown' );
        }
        catch ( \application\exceptions\ValidationException $e )
        {
            $this->assertStringContainsString( 'Некорректный email', $e->getMessage() );
        }

        $user2 = \application\models\User::create( [
            'email'         => 'test@example.com',
            'password_hash' => '',
            'name'          => 'Valid Name'
        ] );

        try
        {
            $user2->validate();
            $this->fail( 'Expected ValidationException was not thrown' );
        }
        catch ( \application\exceptions\ValidationException $e )
        {
            $this->assertStringContainsString( 'Пароль не может быть пустым', $e->getMessage() );
        }

        $user3 = \application\models\User::create( [
            'email'         => 'test@example.com',
            'password_hash' => 'hash',
            'name'          => 'A'
        ] );

        try
        {
            $user3->validate();
            $this->fail( 'Expected ValidationException was not thrown' );
        }
        catch ( \application\exceptions\ValidationException $e )
        {
            $this->assertStringContainsString( 'Имя должно содержать не менее 2 символов', $e->getMessage() );
        }

        $user4 = \application\models\User::create( [
            'email'         => 'test@example.com',
            'password_hash' => 'hash',
            'name'          => ''
        ] );

        try
        {
            $user4->validate();
            $this->fail( 'Expected ValidationException was not thrown' );
        }
        catch ( \application\exceptions\ValidationException $e )
        {
            $this->assertStringContainsString( 'Имя должно содержать не менее 2 символов', $e->getMessage() );
        }
    }

    public function testRegisterDuplicateEmailException() : void
    {
        $mockDb = $this->createMock( \Krugozor\Database\Mysql::class );
        $mockStmt = $this->createMock( \Krugozor\Database\Statement::class );

        $mockDb->method( 'query' )->willReturn( $mockStmt );
        $mockStmt->method( 'fetchAssoc' )->willReturn( [
            'id'            => 1,
            'email'         => 'existing@example.com',
            'password_hash' => 'existing_hash',
            'name'          => 'Existing User',
            'role'          => 'user'
        ] );

        $reflectionClass = new \ReflectionClass( \application\core\Model::class );
        $reflectionProperty = $reflectionClass->getProperty( '_connection' );
        $reflectionProperty->setValue( null, $mockDb );

        try
        {
            \application\models\User::register( [
                'email'    => 'existing@example.com',
                'password' => 'password123',
                'name'     => 'New User'
            ] );
            $this->fail( 'Expected ValidationException for duplicate email was not thrown' );
        }
        catch ( \application\exceptions\ValidationException $e )
        {
            $this->assertStringContainsString( 'Пользователя с таким email уже существует', $e->getMessage() );
        }
    }

}
