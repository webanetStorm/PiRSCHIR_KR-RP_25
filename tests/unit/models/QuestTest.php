<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 03.12.2025
 * Time: 20:08
 */

namespace tests\unit\models;


class QuestTest extends \PHPUnit\Framework\TestCase
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

    private function mockSelectMultipleResult( array $rows ) : void
    {
        $this->_mockDb->method( 'query' )->willReturn( $this->_mockStmt );
        $this->_mockStmt->method( 'fetchAssocArray' )->willReturn( $rows );
    }

    public function testCreateByArray() : void
    {
        $time = time();
        $data = [
            'user_id'          => 1,
            'title'            => 'Test Quest',
            'description'      => 'Test Description',
            'type'             => \application\models\Quest::TYPE_INDIVIDUAL,
            'reward'           => 50,
            'min_participants' => 0,
            'deadline'         => null,
            'status'           => \application\models\Quest::STATUS_DRAFT
        ];

        $quest = \application\models\Quest::createByArray( $data );

        $this->assertSame( 1, $quest->user_id );
        $this->assertSame( 'Test Quest', $quest->title );
        $this->assertSame( 'Test Description', $quest->description );
        $this->assertSame( \application\models\Quest::TYPE_INDIVIDUAL, $quest->type );
        $this->assertSame( 50, $quest->reward );
        $this->assertSame( 0, $quest->min_participants );
        $this->assertNull( $quest->deadline );
        $this->assertSame( \application\models\Quest::STATUS_DRAFT, $quest->status );
        $this->assertGreaterThanOrEqual( $time, $quest->created_at );
        $this->assertGreaterThanOrEqual( $time, $quest->updated_at );
    }

    public function testCreateByArrayWithDeadline() : void
    {
        $deadline = '2026-12-31 23:59:59';
        $data = [
            'user_id'          => 2,
            'title'            => 'Quest With Deadline',
            'description'      => 'Description',
            'type'             => \application\models\Quest::TYPE_TIMED,
            'reward'           => 100,
            'min_participants' => 0,
            'deadline'         => $deadline,
            'status'           => \application\models\Quest::STATUS_ACTIVE
        ];

        $quest = \application\models\Quest::createByArray( $data );

        $this->assertSame( $deadline, $quest->deadline );
        $this->assertSame( \application\models\Quest::TYPE_TIMED, $quest->type );
        $this->assertSame( \application\models\Quest::STATUS_ACTIVE, $quest->status );
    }

    public function testCreateByArrayWithCollectiveType() : void
    {
        $data = [
            'user_id'          => 3,
            'title'            => 'Collective Quest',
            'description'      => 'Description',
            'type'             => \application\models\Quest::TYPE_COLLECTIVE,
            'reward'           => 75,
            'min_participants' => 3,
            'deadline'         => null,
            'status'           => \application\models\Quest::STATUS_DRAFT
        ];

        $quest = \application\models\Quest::createByArray( $data );

        $this->assertSame( \application\models\Quest::TYPE_COLLECTIVE, $quest->type );
        $this->assertSame( 3, $quest->min_participants );
    }

    public function testCreateByArrayWithDefaultValues() : void
    {
        $data = [
            'user_id'     => 4,
            'title'       => 'Minimal Quest',
            'description' => 'Description'
        ];

        $quest = \application\models\Quest::createByArray( $data );

        $this->assertSame( \application\models\Quest::TYPE_INDIVIDUAL, $quest->type );
        $this->assertSame( 20, $quest->reward );
        $this->assertSame( 0, $quest->min_participants );
        $this->assertNull( $quest->deadline );
        $this->assertSame( \application\models\Quest::STATUS_DRAFT, $quest->status );
    }

    public function testValidateSuccessIndividual() : void
    {
        $quest = new \application\models\Quest();
        $quest->title = 'Valid Quest Title';
        $quest->description = 'Valid quest description';
        $quest->type = \application\models\Quest::TYPE_INDIVIDUAL;
        $quest->reward = 50;
        $quest->min_participants = 0;
        $quest->deadline = null;

        $this->expectNotToPerformAssertions();
        $quest->validate();
    }

    public function testValidateSuccessCollective() : void
    {
        $quest = new \application\models\Quest();
        $quest->title = 'Collective Quest';
        $quest->description = 'Description';
        $quest->type = \application\models\Quest::TYPE_COLLECTIVE;
        $quest->reward = 100;
        $quest->min_participants = 2;
        $quest->deadline = null;

        $this->expectNotToPerformAssertions();
        $quest->validate();
    }

    public function testValidateSuccessTimed() : void
    {
        $quest = new \application\models\Quest();
        $quest->title = 'Timed Quest';
        $quest->description = 'Description';
        $quest->type = \application\models\Quest::TYPE_TIMED;
        $quest->reward = 75;
        $quest->min_participants = 0;
        $quest->deadline = date( 'Y-m-d H:i:s', time() + 86400 );

        $this->expectNotToPerformAssertions();
        $quest->validate();
    }

    public function testValidateEmptyTitle() : void
    {
        $quest = new \application\models\Quest();
        $quest->title = '';
        $quest->description = 'Description';
        $quest->type = \application\models\Quest::TYPE_INDIVIDUAL;
        $quest->reward = 50;

        $this->expectException( \application\exceptions\ValidationException::class );
        $quest->validate();
    }

    public function testValidateShortTitle() : void
    {
        $quest = new \application\models\Quest();
        $quest->title = 'AB';
        $quest->description = 'Description';
        $quest->type = \application\models\Quest::TYPE_INDIVIDUAL;
        $quest->reward = 50;

        $this->expectException( \application\exceptions\ValidationException::class );
        $quest->validate();
    }

    public function testValidateEmptyDescription() : void
    {
        $quest = new \application\models\Quest();
        $quest->title = 'Valid Title';
        $quest->description = '';
        $quest->type = \application\models\Quest::TYPE_INDIVIDUAL;
        $quest->reward = 50;

        $this->expectException( \application\exceptions\ValidationException::class );
        $quest->validate();
    }

    public function testValidateInvalidType() : void
    {
        $quest = new \application\models\Quest();
        $quest->title = 'Valid Title';
        $quest->description = 'Description';
        $quest->type = 'invalid_type';
        $quest->reward = 50;

        $this->expectException( \application\exceptions\ValidationException::class );
        $quest->validate();
    }

    public function testValidateRewardTooLow() : void
    {
        $quest = new \application\models\Quest();
        $quest->title = 'Valid Title';
        $quest->description = 'Description';
        $quest->type = \application\models\Quest::TYPE_INDIVIDUAL;
        $quest->reward = 0;

        $this->expectException( \application\exceptions\ValidationException::class );
        $quest->validate();
    }

    public function testValidateRewardTooHigh() : void
    {
        $quest = new \application\models\Quest();
        $quest->title = 'Valid Title';
        $quest->description = 'Description';
        $quest->type = \application\models\Quest::TYPE_INDIVIDUAL;
        $quest->reward = 1001;

        $this->expectException( \application\exceptions\ValidationException::class );
        $quest->validate();
    }

    public function testValidateCollectiveWithInsufficientParticipants() : void
    {
        $quest = new \application\models\Quest();
        $quest->title = 'Collective Quest';
        $quest->description = 'Description';
        $quest->type = \application\models\Quest::TYPE_COLLECTIVE;
        $quest->reward = 100;
        $quest->min_participants = 1;

        $this->expectException( \application\exceptions\ValidationException::class );
        $quest->validate();
    }

    public function testValidateTimedWithoutDeadline() : void
    {
        $quest = new \application\models\Quest();
        $quest->title = 'Timed Quest';
        $quest->description = 'Description';
        $quest->type = \application\models\Quest::TYPE_TIMED;
        $quest->reward = 75;
        $quest->deadline = null;

        $this->expectException( \application\exceptions\ValidationException::class );
        $quest->validate();
    }

    public function testValidateTimedWithPastDeadline() : void
    {
        $quest = new \application\models\Quest();
        $quest->title = 'Timed Quest';
        $quest->description = 'Description';
        $quest->type = \application\models\Quest::TYPE_TIMED;
        $quest->reward = 75;
        $quest->deadline = '2020-01-01 00:00:00';

        $this->expectException( \application\exceptions\ValidationException::class );
        $quest->validate();
    }

    public function testSaveInsert() : void
    {
        $this->_mockDb->expects( $this->once() )->method( 'query' )->willReturn( $this->_mockStmt );
        $this->_mockDb->method( 'getLastInsertId' )->willReturn( 123 );

        $quest = new \application\models\Quest();
        $quest->user_id = 1;
        $quest->title = 'New Quest';
        $quest->description = 'Description';
        $quest->type = \application\models\Quest::TYPE_INDIVIDUAL;
        $quest->reward = 50;
        $quest->min_participants = 0;
        $quest->deadline = null;
        $quest->status = \application\models\Quest::STATUS_DRAFT;
        $quest->created_at = time();
        $quest->updated_at = time();

        $quest->save();

        $this->assertSame( 123, $quest->id );
    }

    public function testSaveUpdate() : void
    {
        $this->_mockDb->expects( $this->once() )->method( 'query' )->willReturn( $this->_mockStmt );

        $quest = new \application\models\Quest();
        $quest->id = 456;
        $quest->user_id = 1;
        $quest->title = 'Updated Quest';
        $quest->description = 'Updated Description';
        $quest->type = \application\models\Quest::TYPE_INDIVIDUAL;
        $quest->reward = 60;
        $quest->min_participants = 0;
        $quest->deadline = null;
        $quest->status = \application\models\Quest::STATUS_ACTIVE;
        $quest->created_at = time() - 3600;
        $quest->updated_at = time() - 1800;

        $quest->save();

        $this->assertSame( 456, $quest->id );
    }

    public function testSaveWithDeadline() : void
    {
        $this->_mockDb->expects( $this->once() )->method( 'query' )->willReturn( $this->_mockStmt );
        $this->_mockDb->method( 'getLastInsertId' )->willReturn( 789 );

        $quest = new \application\models\Quest();
        $quest->user_id = 1;
        $quest->title = 'Quest With Deadline';
        $quest->description = 'Description';
        $quest->type = \application\models\Quest::TYPE_TIMED;
        $quest->reward = 100;
        $quest->min_participants = 0;
        $quest->deadline = '2024-12-31 23:59:59';
        $quest->status = \application\models\Quest::STATUS_DRAFT;
        $quest->created_at = time();
        $quest->updated_at = time();

        $quest->save();

        $this->assertSame( 789, $quest->id );
    }

    public function testFindByIdFound() : void
    {
        $row = [
            'id'               => 42,
            'user_id'          => 5,
            'title'            => 'Found Quest',
            'description'      => 'Found Description',
            'type'             => \application\models\Quest::TYPE_COLLECTIVE,
            'reward'           => 80,
            'min_participants' => 3,
            'deadline'         => null,
            'status'           => \application\models\Quest::STATUS_ACTIVE,
            'created_at'       => 1700000000,
            'updated_at'       => 1700000000
        ];

        $this->mockSelectResult( $row );

        $quest = \application\models\Quest::findById( 42 );

        $this->assertInstanceOf( \application\models\Quest::class, $quest );
        $this->assertSame( 42, $quest->id );
        $this->assertSame( 5, $quest->user_id );
        $this->assertSame( 'Found Quest', $quest->title );
        $this->assertSame( 'Found Description', $quest->description );
        $this->assertSame( \application\models\Quest::TYPE_COLLECTIVE, $quest->type );
        $this->assertSame( 80, $quest->reward );
        $this->assertSame( 3, $quest->min_participants );
        $this->assertNull( $quest->deadline );
        $this->assertSame( \application\models\Quest::STATUS_ACTIVE, $quest->status );
        $this->assertSame( 1700000000, $quest->created_at );
        $this->assertSame( 1700000000, $quest->updated_at );
    }

    public function testFindByIdNotFound() : void
    {
        $this->mockSelectResult( null );

        $quest = \application\models\Quest::findById( 999 );

        $this->assertNull( $quest );
    }

    public function testFindByUserId() : void
    {
        $rows = [
            [
                'id'               => 1,
                'user_id'          => 10,
                'title'            => 'Quest 1',
                'description'      => 'Desc 1',
                'type'             => \application\models\Quest::TYPE_INDIVIDUAL,
                'reward'           => 30,
                'min_participants' => 0,
                'deadline'         => null,
                'status'           => \application\models\Quest::STATUS_DRAFT,
                'created_at'       => 1700000001,
                'updated_at'       => 1700000001
            ],
            [
                'id'               => 2,
                'user_id'          => 10,
                'title'            => 'Quest 2',
                'description'      => 'Desc 2',
                'type'             => \application\models\Quest::TYPE_COLLECTIVE,
                'reward'           => 90,
                'min_participants' => 2,
                'deadline'         => null,
                'status'           => \application\models\Quest::STATUS_ACTIVE,
                'created_at'       => 1700000002,
                'updated_at'       => 1700000002
            ]
        ];

        $this->mockSelectMultipleResult( $rows );

        $quests = \application\models\Quest::findByUserId( 10 );

        $this->assertCount( 2, $quests );
        $this->assertInstanceOf( \application\models\Quest::class, $quests[0] );
        $this->assertSame( 1, $quests[0]->id );
        $this->assertSame( 10, $quests[0]->user_id );
        $this->assertInstanceOf( \application\models\Quest::class, $quests[1] );
        $this->assertSame( 2, $quests[1]->id );
        $this->assertSame( 10, $quests[1]->user_id );
    }

    public function testFindByUserIdEmpty() : void
    {
        $this->mockSelectMultipleResult( [] );

        $quests = \application\models\Quest::findByUserId( 999 );

        $this->assertIsArray( $quests );
        $this->assertEmpty( $quests );
    }

    public function testGetActive() : void
    {
        $rows = [
            [
                'id'               => 3,
                'user_id'          => 20,
                'title'            => 'Active Quest 1',
                'description'      => 'Active Desc 1',
                'type'             => \application\models\Quest::TYPE_INDIVIDUAL,
                'reward'           => 40,
                'min_participants' => 0,
                'deadline'         => null,
                'status'           => \application\models\Quest::STATUS_ACTIVE,
                'created_at'       => 1700000003,
                'updated_at'       => 1700000003
            ],
            [
                'id'               => 4,
                'user_id'          => 21,
                'title'            => 'Active Quest 2',
                'description'      => 'Active Desc 2',
                'type'             => \application\models\Quest::TYPE_TIMED,
                'reward'           => 110,
                'min_participants' => 0,
                'deadline'         => '2024-12-31 23:59:59',
                'status'           => \application\models\Quest::STATUS_ACTIVE,
                'created_at'       => 1700000004,
                'updated_at'       => 1700000004
            ]
        ];

        $this->mockSelectMultipleResult( $rows );

        $quests = \application\models\Quest::getActive();

        $this->assertCount( 2, $quests );
        $this->assertSame( \application\models\Quest::STATUS_ACTIVE, $quests[0]->status );
        $this->assertSame( \application\models\Quest::STATUS_ACTIVE, $quests[1]->status );
        $this->assertSame( 3, $quests[0]->id );
        $this->assertSame( 4, $quests[1]->id );
    }

    public function testGetActiveEmpty() : void
    {
        $this->mockSelectMultipleResult( [] );

        $quests = \application\models\Quest::getActive();

        $this->assertIsArray( $quests );
        $this->assertEmpty( $quests );
    }

    public function testDeleteById() : void
    {
        $this->_mockDb->expects( $this->once() )->method( 'query' )->willReturn( $this->_mockStmt );

        \application\models\Quest::deleteById( 123 );
    }

    public function testToArray() : void
    {
        $quest = new \application\models\Quest();
        $quest->id = 99;
        $quest->user_id = 33;
        $quest->title = 'Array Test Quest';
        $quest->description = 'Array Test Description';
        $quest->type = \application\models\Quest::TYPE_COLLECTIVE;
        $quest->reward = 150;
        $quest->min_participants = 4;
        $quest->deadline = '2024-12-25 18:00:00';
        $quest->status = \application\models\Quest::STATUS_COMPLETED;
        $quest->created_at = 1700000099;
        $quest->updated_at = 1700000100;

        $expected = [
            'id'               => 99,
            'user_id'          => 33,
            'title'            => 'Array Test Quest',
            'description'      => 'Array Test Description',
            'type'             => \application\models\Quest::TYPE_COLLECTIVE,
            'reward'           => 150,
            'min_participants' => 4,
            'deadline'         => '2024-12-25 18:00:00',
            'status'           => \application\models\Quest::STATUS_COMPLETED,
            'created_at'       => 1700000099,
            'updated_at'       => 1700000100
        ];

        $this->assertSame( $expected, $quest->toArray() );
    }

    public function testToArrayWithNullDeadline() : void
    {
        $quest = new \application\models\Quest();
        $quest->id = 100;
        $quest->user_id = 34;
        $quest->title = 'No Deadline Quest';
        $quest->description = 'Description';
        $quest->type = \application\models\Quest::TYPE_INDIVIDUAL;
        $quest->reward = 50;
        $quest->min_participants = 0;
        $quest->deadline = null;
        $quest->status = \application\models\Quest::STATUS_DRAFT;
        $quest->created_at = 1700000200;
        $quest->updated_at = 1700000200;

        $array = $quest->toArray();

        $this->assertNull( $array['deadline'] );
    }

    public function testConstants() : void
    {
        $this->assertSame( 'individual', \application\models\Quest::TYPE_INDIVIDUAL );
        $this->assertSame( 'collective', \application\models\Quest::TYPE_COLLECTIVE );
        $this->assertSame( 'timed', \application\models\Quest::TYPE_TIMED );
        $this->assertSame( 'draft', \application\models\Quest::STATUS_DRAFT );
        $this->assertSame( 'active', \application\models\Quest::STATUS_ACTIVE );
        $this->assertSame( 'completed', \application\models\Quest::STATUS_COMPLETED );
    }

    public function testCreateFromRow() : void
    {
        $row = [
            'id'               => 77,
            'user_id'          => 88,
            'title'            => 'Row Quest',
            'description'      => 'Row Description',
            'type'             => \application\models\Quest::TYPE_TIMED,
            'reward'           => 200,
            'min_participants' => 0,
            'deadline'         => '2024-06-15 12:00:00',
            'status'           => \application\models\Quest::STATUS_ACTIVE,
            'created_at'       => '1700000077',
            'updated_at'       => '1700000078'
        ];

        $reflection = new \ReflectionClass( \application\models\Quest::class );
        $method = $reflection->getMethod( 'createFromRow' );

        $quest = $method->invoke( null, $row );

        $this->assertInstanceOf( \application\models\Quest::class, $quest );
        $this->assertSame( 77, $quest->id );
        $this->assertSame( 88, $quest->user_id );
        $this->assertSame( 'Row Quest', $quest->title );
        $this->assertSame( 'Row Description', $quest->description );
        $this->assertSame( \application\models\Quest::TYPE_TIMED, $quest->type );
        $this->assertSame( 200, $quest->reward );
        $this->assertSame( 0, $quest->min_participants );
        $this->assertSame( '2024-06-15 12:00:00', $quest->deadline );
        $this->assertSame( \application\models\Quest::STATUS_ACTIVE, $quest->status );
        $this->assertSame( 1700000077, $quest->created_at );
        $this->assertSame( 1700000078, $quest->updated_at );
    }

    public function testValidateMethodExists() : void
    {
        $quest = new \application\models\Quest();

        $this->assertTrue( method_exists( $quest, 'validate' ) );

        $reflection = new \ReflectionMethod( \application\models\Quest::class, 'validate' );
        $this->assertTrue( $reflection->isPublic() );
    }

    public function testValidateBoundaryValues() : void
    {
        $quest = new \application\models\Quest();
        $quest->title = 'ABC';
        $quest->description = 'Valid';
        $quest->type = \application\models\Quest::TYPE_INDIVIDUAL;
        $quest->reward = 1;

        $this->expectNotToPerformAssertions();
        $quest->validate();

        $quest->reward = 1000;
        $quest->validate();
    }

    public function testValidateCollectiveBoundaryParticipants() : void
    {
        $quest = new \application\models\Quest();
        $quest->title = 'Collective';
        $quest->description = 'Valid';
        $quest->type = \application\models\Quest::TYPE_COLLECTIVE;
        $quest->reward = 100;
        $quest->min_participants = 2;

        $this->expectNotToPerformAssertions();
        $quest->validate();
    }

    public function testSaveUpdatesTimestamp() : void
    {
        $originalTime = time() - 3600;
        $this->_mockDb->method( 'query' )->willReturn( $this->_mockStmt );
        $this->_mockDb->method( 'getLastInsertId' )->willReturn( 500 );

        $quest = new \application\models\Quest();
        $quest->id = 500;
        $quest->user_id = 1;
        $quest->title = 'Timestamp Test';
        $quest->description = 'Test';
        $quest->type = \application\models\Quest::TYPE_INDIVIDUAL;
        $quest->reward = 50;
        $quest->created_at = $originalTime;
        $quest->updated_at = $originalTime;

        $quest->save();

        $this->assertGreaterThanOrEqual( $originalTime, $quest->created_at );
    }

    public function testValidationExceptionMessages() : void
    {
        $quest1 = new \application\models\Quest();
        $quest1->title = '';
        $quest1->description = 'Valid';
        $quest1->type = \application\models\Quest::TYPE_INDIVIDUAL;
        $quest1->reward = 50;

        try
        {
            $quest1->validate();
            $this->fail( 'Expected ValidationException was not thrown' );
        }
        catch ( \application\exceptions\ValidationException $e )
        {
            $this->assertStringContainsString( 'Название квеста должно содержать не менее 3 символов', $e->getMessage() );
        }

        $quest2 = new \application\models\Quest();
        $quest2->title = 'Valid';
        $quest2->description = '';
        $quest2->type = \application\models\Quest::TYPE_INDIVIDUAL;
        $quest2->reward = 50;

        try
        {
            $quest2->validate();
            $this->fail( 'Expected ValidationException was not thrown' );
        }
        catch ( \application\exceptions\ValidationException $e )
        {
            $this->assertStringContainsString( 'Описание квеста не может быть пустым', $e->getMessage() );
        }

        $quest3 = new \application\models\Quest();
        $quest3->title = 'Valid';
        $quest3->description = 'Valid';
        $quest3->type = 'invalid';
        $quest3->reward = 50;

        try
        {
            $quest3->validate();
            $this->fail( 'Expected ValidationException was not thrown' );
        }
        catch ( \application\exceptions\ValidationException $e )
        {
            $this->assertStringContainsString( 'Неверный тип квеста', $e->getMessage() );
        }

        $quest4 = new \application\models\Quest();
        $quest4->title = 'Valid';
        $quest4->description = 'Valid';
        $quest4->type = \application\models\Quest::TYPE_COLLECTIVE;
        $quest4->reward = 100;
        $quest4->min_participants = 1;

        try
        {
            $quest4->validate();
            $this->fail( 'Expected ValidationException was not thrown' );
        }
        catch ( \application\exceptions\ValidationException $e )
        {
            $this->assertStringContainsString( 'Для коллективного квеста нужно минимум 2 участника', $e->getMessage() );
        }

        $quest5 = new \application\models\Quest();
        $quest5->title = 'Valid';
        $quest5->description = 'Valid';
        $quest5->type = \application\models\Quest::TYPE_TIMED;
        $quest5->reward = 100;
        $quest5->deadline = null;

        try
        {
            $quest5->validate();
            $this->fail( 'Expected ValidationException was not thrown' );
        }
        catch ( \application\exceptions\ValidationException $e )
        {
            $this->assertStringContainsString( 'Для квеста с лимитом времени обязательна дата', $e->getMessage() );
        }

        $quest6 = new \application\models\Quest();
        $quest6->title = 'Valid';
        $quest6->description = 'Valid';
        $quest6->type = \application\models\Quest::TYPE_TIMED;
        $quest6->reward = 100;
        $quest6->deadline = '2020-01-01 00:00:00';

        try
        {
            $quest6->validate();
            $this->fail( 'Expected ValidationException was not thrown' );
        }
        catch ( \application\exceptions\ValidationException $e )
        {
            $this->assertStringContainsString( 'Нельзя установить дедлайн задним числом', $e->getMessage() );
        }
    }

}
