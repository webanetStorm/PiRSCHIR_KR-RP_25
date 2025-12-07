<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 03.12.2025
 * Time: 16:31
 */

namespace tests\unit\services;


class QuestServiceTest extends \PHPUnit\Framework\TestCase
{

    private \application\services\QuestService $_service;

    private \application\repositories\QuestRepository $_mockRepo;


    protected function setUp() : void
    {
        $this->_mockRepo = $this->createMock( \application\repositories\QuestRepository::class );
        $this->_service = new \application\services\QuestService( $this->_mockRepo );
    }

    public function testCreateSavesQuestSuccessfully() : void
    {
        $user = new \application\models\User( [
            'id'    => 1,
            'email' => 'test@example.com',
            'name'  => 'Test User',
            'role'  => 'user'
        ] );
        $data = [
            'title'       => 'Test Quest',
            'description' => 'Test Description',
            'type'        => 'individual',
            'reward'      => 10
        ];

        $this->_mockRepo->expects( $this->once() )->method( 'save' );

        $quest = $this->_service->create( $data, $user );

        $this->assertEquals( 'Test Quest', $quest->title );
        $this->assertEquals( 1, $quest->user_id );
        $this->assertGreaterThan( 0, $quest->created_at );
    }

    public function testUpdateThrowsExceptionIfNotDraft() : void
    {
        $quest = new \application\models\Quest( [
            'id'      => 1,
            'user_id' => 1,
            'status'  => 'active',
            'title'   => 'Active Quest'
        ] );

        $this->expectException( \application\exceptions\ValidationException::class );
        $this->expectExceptionMessage( 'Редактировть можно только квесты в черновиках' );

        $this->_service->update( $quest, [ 'title' => 'New Title' ], $quest->user_id );
    }

    public function testPublishDraftQuestSuccessfully() : void
    {
        $quest = new \application\models\Quest( [
            'id'     => 1,
            'status' => 'draft',
            'title'  => 'Draft Quest'
        ] );

        $this->_mockRepo->expects( $this->once() )->method( 'save' );

        $publishedQuest = $this->_service->publish( $quest );

        $this->assertEquals( 'active', $publishedQuest->status );
        $this->assertGreaterThanOrEqual( $quest->updated_at, $publishedQuest->updated_at );
    }

    public function testPublishThrowsExceptionIfNotDraft() : void
    {
        $quest = new \application\models\Quest( [
            'id'     => 1,
            'status' => 'active',
            'title'  => 'Active Quest'
        ] );

        $this->expectException( \application\exceptions\ValidationException::class );
        $this->expectExceptionMessage( 'Можно публиковать только черновики' );

        $this->_service->publish( $quest );
    }

    public function testDeleteThrowsExceptionIfNotDraft() : void
    {
        $quest = new \application\models\Quest( [
            'id'      => 1,
            'user_id' => 1,
            'status'  => 'active',
            'title'   => 'Active Quest'
        ] );

        $this->expectException( \application\exceptions\ValidationException::class );
        $this->expectExceptionMessage( 'Удалять можно только квесты в черновиках' );

        $this->_service->delete( $quest );
    }

}
