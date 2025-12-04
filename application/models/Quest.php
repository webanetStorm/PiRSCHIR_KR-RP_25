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

    public const string TYPE_INDIVIDUAL = 'individual';

    public const string TYPE_COLLECTIVE = 'collective';

    public const string TYPE_TIMED = 'timed';

    public const string STATUS_DRAFT = 'draft';

    public const string STATUS_ACTIVE = 'active';

    public const string STATUS_COMPLETED = 'completed';


    public int $id = 0;

    public int $user_id = 0;

    public string $title = '';

    public string $description = '';

    public string $type = self::TYPE_INDIVIDUAL;

    public int $reward = 0;

    public ?int $min_participants = null;

    public ?string $deadline = null;

    public string $status = self::STATUS_DRAFT;

    public int $created_at = 0;

    public int $updated_at = 0;


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

    /**
     * @throws \application\exceptions\ValidationException
     */
    public function validate() : void
    {
        if ( empty( $this->title ) || mb_strlen( $this->title ) < 3 )
        {
            throw new \application\exceptions\ValidationException( 'Название квеста должно содержать не менее 3 символов' );
        }

        if ( empty( $this->description ) )
        {
            throw new \application\exceptions\ValidationException( 'Описание квеста не может быть пустым' );
        }

        if ( !in_array( $this->type, [ self::TYPE_INDIVIDUAL, self::TYPE_COLLECTIVE, self::TYPE_TIMED ] ) )
        {
            throw new \application\exceptions\ValidationException( 'Неверный тип квеста' );
        }

        if ( $this->reward < 1 || $this->reward > 1000 )
        {
            throw new \application\exceptions\ValidationException( 'Награда должна быть от 1 до 1000 XP' );
        }

        if ( $this->type === self::TYPE_COLLECTIVE && $this->min_participants < 2 )
        {
            throw new \application\exceptions\ValidationException( 'Для коллективного квеста нужно минимум 2 участника' );
        }

        if ( $this->type === self::TYPE_TIMED && !$this->deadline )
        {
            throw new \application\exceptions\ValidationException( 'Для квеста с лимитом времени обязательна дата' );
        }

        if ( $this->deadline && strtotime( $this->deadline ) <= time() )
        {
            throw new \application\exceptions\ValidationException( 'Нельзя установить дедлайн задним числом' );
        }
    }

}
