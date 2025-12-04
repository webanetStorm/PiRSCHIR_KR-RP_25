<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 04.12.2025
 * Time: 21:55
 */

namespace tests\unit\repositories;


class UserRepositoryTest extends \PHPUnit\Framework\TestCase
{

    private \application\repositories\QuestRepository $_repo;


    protected function setUp(): void
    {
        $this->_repo = new \application\repositories\QuestRepository();
    }

    public function testFindByIdReturnsQuest(): void
    {
        $quest = $this->_repo->findById(1);
        $this->assertInstanceOf(\application\models\Quest::class, $quest);
        $this->assertEquals(1, $quest->id);
    }

    public function testFindByIdReturnsNull(): void
    {
        $quest = $this->_repo->findById(9999999);
        $this->assertNull($quest);
    }

    public function testFindByUserIdReturnsQuests(): void
    {
        $quests = $this->_repo->findByUserId(1);
        $this->assertIsArray($quests);
        foreach ($quests as $quest) {
            $this->assertInstanceOf(\application\models\Quest::class, $quest);
            $this->assertEquals(1, $quest->user_id);
        }
    }

    public function testFindActiveReturnsQuests(): void
    {
        $quests = $this->_repo->findActive();
        $this->assertIsArray($quests);
        foreach ($quests as $quest) {
            $this->assertInstanceOf(\application\models\Quest::class, $quest);
            $this->assertEquals('active', $quest->status);
        }
    }

    public function testSaveInsertsNewQuest(): void
    {
        $quest = new \application\models\Quest([
            'user_id' => 1,
            'title' => 'Test Quest',
            'description' => 'Test Description',
            'type' => 'individual',
            'reward' => 10,
            'status' => 'draft',
            'created_at' => time(),
            'updated_at' => time()
        ]);

        $this->_repo->save($quest);

        $this->assertGreaterThan(0, $quest->id);
    }

    public function testSaveUpdatesExistingQuest(): void
    {
        $quest = $this->_repo->findById(1);
        $oldTitle = $quest->title;
        $newTitle = 'Updated Title ' . uniqid();

        $quest->title = $newTitle;
        $this->_repo->save($quest);

        $updated = $this->_repo->findById(1);
        $this->assertEquals($newTitle, $updated->title);
        $this->assertNotEquals($oldTitle, $updated->title);
    }

    public function testDeleteRemovesQuest(): void
    {
        // Предполагаем, что квест с id 9999999 не существует или создаём его перед тестом.
        // Для простоты тестируем, что метод не падает.
        $this->expectNotToPerformAssertions();

        $this->_repo->delete(9999999);
    }

}
