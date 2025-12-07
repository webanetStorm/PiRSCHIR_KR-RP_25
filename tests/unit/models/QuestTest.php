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

    public function testToArray()
    {
        $q = new \application\models\Quest();
        $q->id = 10;
        $q->user_id = 5;
        $q->title = "Test";
        $q->description = "Desc";
        $q->type = \application\models\Quest::TYPE_COLLECTIVE;
        $q->reward = 50;
        $q->min_participants = 3;
        $q->status = \application\models\Quest::STATUS_ACTIVE;
        $q->created_at = 100;
        $q->updated_at = 200;

        $this->assertSame( [
            'id'               => 10,
            'user_id'          => 5,
            'title'            => 'Test',
            'description'      => 'Desc',
            'type'             => 'collective',
            'reward'           => 50,
            'min_participants' => 3,
            'deadline'         => null,
            'status'           => 'active',
            'is_approved'      => false,
            'created_at'       => 100,
            'updated_at'       => 200
        ], $q->toArray() );
    }

    public function testValidateSuccessIndividual()
    {
        $q = new \application\models\Quest();
        $q->title = "abc";
        $q->description = "desc";
        $q->type = \application\models\Quest::TYPE_INDIVIDUAL;
        $q->reward = 10;
        $q->validate();
        $this->assertTrue( true );
    }

    public function testValidateSuccessCollective()
    {
        $q = new \application\models\Quest();
        $q->title = "title";
        $q->description = "desc";
        $q->type = \application\models\Quest::TYPE_COLLECTIVE;
        $q->reward = 10;
        $q->min_participants = 2;
        $q->validate();
        $this->assertTrue( true );
    }

    public function testValidateSuccessTimed()
    {
        $q = new \application\models\Quest();
        $q->title = "title";
        $q->description = "desc";
        $q->type = \application\models\Quest::TYPE_TIMED;
        $q->reward = 10;
        $q->deadline = date( "Y-m-d", time() + 86400 );
        $q->validate();
        $this->assertTrue( true );
    }

    public function testValidateTitleTooShort()
    {
        $this->expectException( \application\exceptions\ValidationException::class );
        $q = new \application\models\Quest();
        $q->title = "a";
        $q->description = "desc";
        $q->reward = 10;
        $q->validate();
    }

    public function testValidateDescriptionEmpty()
    {
        $this->expectException( \application\exceptions\ValidationException::class );
        $q = new \application\models\Quest();
        $q->title = "abc";
        $q->description = "";
        $q->reward = 10;
        $q->validate();
    }

    public function testValidateInvalidType()
    {
        $this->expectException( \application\exceptions\ValidationException::class );
        $q = new \application\models\Quest();
        $q->title = "abc";
        $q->description = "desc";
        $q->type = "wrong";
        $q->reward = 10;
        $q->validate();
    }

    public function testValidateRewardTooSmall()
    {
        $this->expectException( \application\exceptions\ValidationException::class );
        $q = new \application\models\Quest();
        $q->title = "abc";
        $q->description = "desc";
        $q->reward = 0;
        $q->validate();
    }

    public function testValidateRewardTooBig()
    {
        $this->expectException( \application\exceptions\ValidationException::class );
        $q = new \application\models\Quest();
        $q->title = "abc";
        $q->description = "desc";
        $q->reward = 1001;
        $q->validate();
    }

    public function testValidateCollectiveParticipantsTooSmall()
    {
        $this->expectException( \application\exceptions\ValidationException::class );
        $q = new \application\models\Quest();
        $q->title = "abc";
        $q->description = "desc";
        $q->type = \application\models\Quest::TYPE_COLLECTIVE;
        $q->reward = 10;
        $q->min_participants = 1;
        $q->validate();
    }

    public function testValidateTimedWithoutDeadline()
    {
        $this->expectException( \application\exceptions\ValidationException::class );
        $q = new \application\models\Quest();
        $q->title = "abc";
        $q->description = "desc";
        $q->type = \application\models\Quest::TYPE_TIMED;
        $q->reward = 10;
        $q->validate();
    }

    public function testValidateDeadlineInPast()
    {
        $this->expectException( \application\exceptions\ValidationException::class );
        $q = new \application\models\Quest();
        $q->title = "abc";
        $q->description = "desc";
        $q->type = \application\models\Quest::TYPE_TIMED;
        $q->reward = 10;
        $q->deadline = date( "Y-m-d", time() - 86400 );
        $q->validate();
    }

}
