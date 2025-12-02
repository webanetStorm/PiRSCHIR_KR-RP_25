<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 02.12.2025
 * Time: 23:01
 */

namespace application\services;


class QuestService
{

    public static function createQuest( array $data, \application\models\User $user ) : \application\models\Quest
    {
        $quest = \application\models\Quest::createByArray( [
            'user_id'          => $user->id,
            'title'            => $data['title'],
            'description'      => $data['description'],
            'type'             => $data['type'] ?? \application\models\Quest::TYPE_INDIVIDUAL,
            'reward'           => $data['reward'] ?? 20,
            'min_participants' => $data['min_participants'] ?? 0,
            'deadline'         => $data['deadline'] ?? null,
            'status'           => \application\models\Quest::STATUS_DRAFT,
        ] );

        $quest->save();

        return $quest;
    }

    public static function updateQuest( \application\models\Quest $quest, array $data ) : \application\models\Quest
    {
        $quest->title = $data['title'] ?? $quest->title;
        $quest->description = $data['description'] ?? $quest->description;
        $quest->type = $data['type'] ?? $quest->type;
        $quest->reward = (int)( $data['reward'] ?? $quest->reward );
        $quest->min_participants = (int)( $data['min_participants'] ?? $quest->min_participants );
        $quest->deadline = !empty( $data['deadline'] ) ? $data['deadline'] : null;
        $quest->updated_at = time();

        $quest->validate();
        $quest->save();

        return $quest;
    }

    public static function publishQuest( \application\models\Quest $quest ) : \application\models\Quest
    {
        if ( $quest->status !== \application\models\Quest::STATUS_DRAFT )
        {
            throw new \application\exceptions\ValidationException( 'Можно публиковать только черновики' );
        }

        $quest->status = \application\models\Quest::STATUS_ACTIVE;
        $quest->updated_at = time();
        $quest->save();

        return $quest;
    }

    public static function deleteQuest( \application\models\Quest $quest ) : void
    {
        \application\models\Quest::deleteById( $quest->id );
    }

    public static function validateQuestData( array $data ) : array
    {
        $errors = [];

        if ( empty( $data['title'] ) || mb_strlen( $data['title'] ) < 3 )
        {
            $errors['title'] = 'Название квеста должно содержать не менее 3 символов';
        }

        if ( empty( $data['description'] ) )
        {
            $errors['description'] = 'Описание квеста не может быть пустым';
        }

        $validTypes = [ \application\models\Quest::TYPE_INDIVIDUAL, \application\models\Quest::TYPE_COLLECTIVE, \application\models\Quest::TYPE_TIMED ];
        if ( !in_array( $data['type'] ?? '', $validTypes ) )
        {
            $errors['type'] = 'Неверный тип квеста';
        }

        $reward = (int)( $data['reward'] ?? 0 );
        if ( $reward < 1 || $reward > 1000 )
        {
            $errors['reward'] = 'Награда должна быть от 1 до 1000 XP';
        }

        if ( ( $data['type'] ?? '' ) === \application\models\Quest::TYPE_COLLECTIVE && ( (int)( $data['min_participants'] ?? 0 ) < 2 ) )
        {
            $errors['min_participants'] = 'Для коллективного квеста нужно минимум 2 участника';
        }

        if ( ( $data['type'] ?? '' ) === \application\models\Quest::TYPE_TIMED && empty( $data['deadline'] ) )
        {
            $errors['deadline'] = 'Для квеста с лимитом времени обязательна дата';
        }

        if ( !empty( $data['deadline'] ) && strtotime( $data['deadline'] ) <= time() )
        {
            $errors['deadline'] = 'Нельзя установить дедлайн задним числом';
        }

        return $errors;
    }

}
