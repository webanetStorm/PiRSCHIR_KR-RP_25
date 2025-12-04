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

    public function testUpdateDraftQuestSuccessfully() : void
    {
        $quest = new \application\models\Quest( [
            'id'          => 1,
            'user_id'     => 1,
            'status'      => 'draft',
            'title'       => 'Old Title',
            'description' => 'Old Description',
            'reward'      => 100,
            'updated_at'  => time() - 100
        ] );

        $data = [ 'title' => 'Updated Title' ];

        $this->_mockRepo->expects( $this->once() )->method( 'save' );

        $_SESSION['user_id'] = 1;

        $updatedQuest = $this->_service->update( $quest, $data );

        $this->assertEquals( 'Updated Title', $updatedQuest->title );
        $this->assertGreaterThanOrEqual( $quest->updated_at, $updatedQuest->updated_at );
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

        $this->_service->update( $quest, [ 'title' => 'New Title' ] );
    }

    public function testUpdateThrowsExceptionIfNotOwner() : void
    {
        $quest = new \application\models\Quest( [
            'id'      => 1,
            'user_id' => 999,
            'status'  => 'draft',
            'title'   => 'Draft Quest'
        ] );

        $this->expectException( \application\exceptions\ForbiddenException::class );
        $this->expectExceptionMessage( 'Недостаточно прав для редактирования чужих квестов' );

        $_SESSION['user_id'] = 1;

        $this->_service->update( $quest, [ 'title' => 'New Title' ] );
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

    public function testDeleteDraftQuestSuccessfully() : void
    {
        $quest = new \application\models\Quest( [
            'id'      => 1,
            'user_id' => 1,
            'status'  => 'draft',
            'title'   => 'Draft Quest'
        ] );

        $this->_mockRepo->expects( $this->once() )->method( 'delete' )->with( 1 );

        $_SESSION['user_id'] = 1;

        $this->_service->delete( $quest );

        $this->assertTrue( true );
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

    public function testDeleteThrowsExceptionIfNotOwner() : void
    {
        $quest = new \application\models\Quest( [
            'id'      => 1,
            'user_id' => 999,
            'status'  => 'draft',
            'title'   => 'Draft Quest'
        ] );

        $this->expectException( \application\exceptions\ForbiddenException::class );
        $this->expectExceptionMessage( 'Недостаточно прав для удаления чужих квестов' );

        $_SESSION['user_id'] = 1;

        $this->_service->delete( $quest );
    }

}
