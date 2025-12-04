<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 04.12.2025
 * Time: 16:49
 */

namespace application\repositories;


class QuestRepository
{

    /**
     * @throws \Krugozor\Database\MySqlException
     */
    public function findById( int $id ) : ?\application\models\Quest
    {
        $row = \application\core\DB::i()->query( "SELECT * FROM `quests` WHERE `id` = ?i LIMIT 1", $id )->fetchAssoc();

        return $row ? new \application\models\Quest( $row ) : null;
    }

    /**
     * @throws \Krugozor\Database\MySqlException
     */
    public function findByUserId( int $userId ) : array
    {
        $rows = \application\core\DB::i()->query( "SELECT * FROM `quests` WHERE `user_id` = ?i", $userId )->fetchAssocArray();

        return array_map( fn( $r ) => new \application\models\Quest( $r ), $rows );
    }

    /**
     * @throws \Krugozor\Database\MySqlException
     */
    public function findActive() : array
    {
        $rows = \application\core\DB::i()->query( "SELECT * FROM `quests` WHERE `status` = '?s' AND `is_approved` = 1", 'active' )->fetchAssocArray();

        return array_map( fn( $r ) => new \application\models\Quest( $r ), $rows );
    }

    /**
     * @throws \Krugozor\Database\MySqlException
     */
    public function findPendingApproval() : array
    {
        $rows = \application\core\DB::i()->query( "SELECT * FROM `quests` WHERE `is_approved` = 0 ORDER BY `created_at` ASC" )->fetchAssocArray();

        return array_map( fn( $r ) => new \application\models\Quest( $r ), $rows );
    }

    /**
     * @throws \Krugozor\Database\MySqlException
     */
    public function save( \application\models\Quest $quest ) : void
    {
        $deadlinePlaceholder = !$quest->deadline ? '?n' : '\'?s\'';

        if ( $quest->id > 0 )
        {
            \application\core\DB::i()->query( "UPDATE `quests` SET `title` = '?s', `description` = '?s', `type` = '?s', `reward` = ?i, `min_participants` = ?i, `deadline` = $deadlinePlaceholder, `status` = '?s', `is_approved` = ?i, `updated_at` = ?i WHERE `id` = ?i", $quest->title, $quest->description, $quest->type, $quest->reward, $quest->min_participants, $quest->deadline, $quest->status, $quest->is_approved, $quest->updated_at, $quest->id );
        }
        else
        {
            \application\core\DB::i()->query( "INSERT INTO `quests` (`user_id`, `title`, `description`, `type`, `reward`, `min_participants`, `deadline`, `status`, `is_approved`, `created_at`, `updated_at`) VALUES (?i, '?s', '?s', '?s', ?i, ?i, $deadlinePlaceholder, '?s', ?i, ?i, ?i)", $quest->user_id, $quest->title, $quest->description, $quest->type, $quest->reward, $quest->min_participants, $quest->deadline, $quest->status, $quest->is_approved, $quest->created_at, $quest->updated_at );
            $quest->id = \application\core\DB::i()->getLastInsertId();
        }
    }

    /**
     * @throws \Krugozor\Database\MySqlException
     */
    public function delete( int $id ) : void
    {
        \application\core\DB::i()->query( "DELETE FROM `quests` WHERE `id` = ?i", $id );
    }

}
