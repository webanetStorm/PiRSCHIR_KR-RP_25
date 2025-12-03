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

    public function testValidateQuestDataSuccess()
    {
        $data = [
            'title'            => 'Valid Quest',
            'description'      => 'Valid description',
            'type'             => 'individual',
            'reward'           => 100,
            'min_participants' => 1
        ];

        $errors = \application\services\QuestService::validateQuestData( $data );

        $this->assertEmpty( $errors );
    }

    public function testValidateQuestDataEmptyTitle()
    {
        $data = [
            'title'       => '',
            'description' => 'Description',
            'type'        => 'individual'
        ];

        $errors = \application\services\QuestService::validateQuestData( $data );

        $this->assertArrayHasKey( 'title', $errors );
        $this->assertEquals( 'Название квеста должно содержать не менее 3 символов', $errors['title'] );
    }

    public function testValidateQuestDataShortTitle()
    {
        $data = [
            'title'       => 'AB',
            'description' => 'Description',
            'type'        => 'individual'
        ];

        $errors = \application\services\QuestService::validateQuestData( $data );

        $this->assertArrayHasKey( 'title', $errors );
    }

    public function testValidateQuestDataEmptyDescription()
    {
        $data = [
            'title'       => 'Valid Title',
            'description' => '',
            'type'        => 'individual'
        ];

        $errors = \application\services\QuestService::validateQuestData( $data );

        $this->assertArrayHasKey( 'description', $errors );
    }

    public function testValidateQuestDataInvalidType()
    {
        $data = [
            'title'       => 'Valid Title',
            'description' => 'Description',
            'type'        => 'invalid_type'
        ];

        $errors = \application\services\QuestService::validateQuestData( $data );

        $this->assertArrayHasKey( 'type', $errors );
    }

    public function testValidateQuestDataInvalidReward()
    {
        $data = [
            'title'       => 'Valid Title',
            'description' => 'Description',
            'type'        => 'individual',
            'reward'      => 0
        ];

        $errors = \application\services\QuestService::validateQuestData( $data );

        $this->assertArrayHasKey( 'reward', $errors );

        $data['reward'] = 2000;
        $errors = \application\services\QuestService::validateQuestData( $data );
        $this->assertArrayHasKey( 'reward', $errors );
    }

    public function testValidateQuestDataCollectiveWithOneParticipant()
    {
        $data = [
            'title'            => 'Collective Quest',
            'description'      => 'Description',
            'type'             => 'collective',
            'min_participants' => 1
        ];

        $errors = \application\services\QuestService::validateQuestData( $data );

        $this->assertArrayHasKey( 'min_participants', $errors );
    }

    public function testValidateQuestDataTimedWithoutDeadline()
    {
        $data = [
            'title'       => 'Timed Quest',
            'description' => 'Description',
            'type'        => 'timed'
        ];

        $errors = \application\services\QuestService::validateQuestData( $data );

        $this->assertArrayHasKey( 'deadline', $errors );
    }

    public function testValidateQuestDataPastDeadline()
    {
        $data = [
            'title'       => 'Timed Quest',
            'description' => 'Description',
            'type'        => 'timed',
            'deadline'    => '2020-01-01 00:00:00'
        ];

        $errors = \application\services\QuestService::validateQuestData( $data );

        $this->assertArrayHasKey( 'deadline', $errors );
    }

    public function testValidateQuestDataFutureDeadline()
    {
        $data = [
            'title'       => 'Timed Quest',
            'description' => 'Description',
            'type'        => 'timed',
            'deadline'    => date( 'Y-m-d H:i:s', strtotime( '+1 day' ) ),
            'reward'      => 77
        ];

        $errors = \application\services\QuestService::validateQuestData( $data );

        $this->assertEmpty( $errors );
    }

}
