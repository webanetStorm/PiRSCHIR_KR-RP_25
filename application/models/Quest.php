<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 19:05
 */

namespace application\models;


class Quest extends \application\core\Model
{

    public const string TABLE = 'quests';


    public const string TYPE_INDIVIDUAL = 'individual';

    public const string TYPE_COLLECTIVE = 'collective';

    public const string TYPE_TIMED = 'timed';

    public const string STATUS_DRAFT = 'draft';

    public const string STATUS_ACTIVE = 'active';

    public const string STATUS_COMPLETED = 'completed';


    public int $id = 0;

    public int $user_id;

    public string $title;

    public string $description;

    public string $type;

    public int $reward;

    public int $min_participants = 0;

    public ?string $deadline = null;

    public string $status = self::STATUS_DRAFT;

    public int $created_at;

    public int $updated_at;


    public static function createByArray( array $data ) : self
    {
        $quest = new self;

        $quest->user_id = (int)( $data['user_id'] ?? 0 );
        $quest->title = trim( $data['title'] ?? '' );
        $quest->description = trim( $data['description'] ?? '' );
        $quest->type = $data['type'] ?? self::TYPE_INDIVIDUAL;
        $quest->reward = (int)( $data['reward'] ?? 20 );
        $quest->min_participants = (int)( $data['min_participants'] ?? 0 );
        $quest->deadline = !empty( $data['deadline'] ) ? $data['deadline'] : null;
        $quest->status = $data['status'] ?? self::STATUS_DRAFT;
        $quest->created_at = time();
        $quest->updated_at = time();

        $quest->validate();

        return $quest;
    }

    public function validate() : void
    {
        if ( empty( $this->title ) || mb_strlen( $this->title ) < 3 )
        {
            throw new \applications\exceptions\ValidationException( 'Название квеста должно содержать не менее 3 символов' );
        }

        if ( empty( $this->description ) )
        {
            throw new \applications\exceptions\ValidationException( 'Описание квеста не может быть пустым' );
        }

        if ( !in_array( $this->type, [ self::TYPE_INDIVIDUAL, self::TYPE_COLLECTIVE, self::TYPE_TIMED ] ) )
        {
            throw new \applications\exceptions\ValidationException( 'Неверный тип квеста' );
        }

        if ( $this->reward < 1 || $this->reward > 1000 )
        {
            throw new \applications\exceptions\ValidationException( 'Награда должна быть от 1 до 1000 XP' );
        }

        if ( $this->type === self::TYPE_COLLECTIVE && $this->min_participants < 2 )
        {
            throw new \applications\exceptions\ValidationException( 'Для коллективного квеста нужно минимум 2 участника' );
        }

        if ( $this->type === self::TYPE_TIMED && !$this->deadline )
        {
            throw new \applications\exceptions\ValidationException( 'Для квеста с лимитом времени обязательна дата' );
        }

        if ( $this->deadline && strtotime( $this->deadline ) <= time() )
        {
            throw new \applications\exceptions\ValidationException( 'Нельзя установить дедлайн задним числом' );
        }
    }

    public function save() : void
    {
        if ( $this->id )
        {
            self::db()->query( "UPDATE `quests` SET `title` = '?s', `description` = '?s', `type` = '?s', `reward` = ?i, `min_participants` = ?i, `deadline` = '?s', `status` = '?s', `updated_at` = ?i WHERE `id` = ?i", $this->title, $this->description, $this->type, $this->reward, $this->min_participants, $this->deadline, $this->status, time(), $this->id );
        }
        else
        {
            self::db()->query( "INSERT INTO `quests` (`user_id`, `title`, `description`, `type`, `reward`, `min_participants`, `deadline`, `status`, `created_at`, `updated_at`) VALUES (?i, '?s', '?s', '?s', ?i, ?i, '?s', '?s', ?i, ?i)", $this->user_id, $this->title, $this->description, $this->type, $this->reward, $this->min_participants, $this->deadline, $this->status, time(), time() );
            $this->id = self::db()->getLastInsertId();
        }
    }

    public static function findById( int $id ) : ?self
    {
        $row = self::db()->query( "SELECT * FROM `quests` WHERE `id` = ?i LIMIT 1", $id )->fetchAssoc();

        return $row ? self::createFromRow( $row ) : null;
    }

    public static function findByUserId( int $userId ) : array
    {
        $rows = self::db()->query( "SELECT * FROM `quests` WHERE `user_id` = ?i", $userId )->fetchAssocArray();

        return array_map( [ self::class, 'createFromRow' ], $rows );
    }

    public static function getActive() : array
    {
        $rows = self::db()->query( "SELECT * FROM `quests` WHERE `status` = '?s'", 'active' )->fetchAssocArray();

        return array_map( [ self::class, 'createFromRow' ], $rows );
    }

    private static function createFromRow( array $row ) : self
    {
        $quest = new self;

        $quest->id = (int)$row['id'];
        $quest->user_id = (int)$row['user_id'];
        $quest->title = $row['title'];
        $quest->description = $row['description'];
        $quest->type = $row['type'];
        $quest->reward = (int)$row['reward'];
        $quest->min_participants = (int)$row['min_participants'];
        $quest->deadline = $row['deadline'];
        $quest->status = $row['status'];
        $quest->created_at = (int)$row['created_at'];
        $quest->updated_at = (int)$row['updated_at'];

        return $quest;
    }

    public function toArray() : array
    {
        return [
            'id'               => $this->id,
            'user_id'          => $this->user_id,
            'title'            => $this->title,
            'description'      => $this->description,
            'type'             => $this->type,
            'reward'           => $this->reward,
            'min_participants' => $this->min_participants,
            'deadline'         => $this->deadline,
            'status'           => $this->status,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at
        ];
    }

}
