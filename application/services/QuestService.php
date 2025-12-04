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

    private \application\repositories\QuestRepository $_repository;


    public function __construct( \application\repositories\QuestRepository $repository )
    {
        $this->_repository = $repository;
    }

    /**
     * @throws \application\exceptions\ValidationException
     */
    public function create( array $data, \application\models\User $user ) : \application\models\Quest
    {
        $quest = new \application\models\Quest();
        $time = time();

        $quest->fill( array_merge( $data, [
            'user_id'    => $user->id,
            'created_at' => $time,
            'updated_at' => $time
        ] ) );

        $quest->validate();
        $this->_repository->save( $quest );

        return $quest;
    }

    /**
     * @throws \application\exceptions\ValidationException
     * @throws \application\exceptions\ForbiddenException
     * @throws \Krugozor\Database\MySqlException
     */
    public function update( \application\models\Quest $quest, array $data ) : \application\models\Quest
    {
        if ( $quest->status !== 'draft' )
        {
            throw new \application\exceptions\ValidationException( 'Редактировть можно только квесты в черновиках' );
        }

        if ( $quest->user_id !== new UserService( new \application\repositories\UserRepository )->getCurrentUser()?->id ?? 0 )
        {
            throw new \application\exceptions\ForbiddenException( 'Недостаточно прав для редактирования чужих квестов' );
        }

        $quest->fill( $data );
        $quest->updated_at = time();

        $quest->validate();
        $this->_repository->save( $quest );

        return $quest;
    }

    /**
     * @throws \Krugozor\Database\MySqlException
     * @throws \application\exceptions\ValidationException
     */
    public function publish( \application\models\Quest $quest ) : \application\models\Quest
    {
        if ( $quest->status !== 'draft' )
        {
            throw new \application\exceptions\ValidationException( 'Можно публиковать только черновики' );
        }

        $quest->status = 'active';
        $quest->updated_at = time();
        $this->_repository->save( $quest );

        return $quest;
    }

    /**
     * @throws \Krugozor\Database\MySqlException
     * @throws \application\exceptions\ForbiddenException
     * @throws \application\exceptions\ValidationException
     */
    public function delete( \application\models\Quest $quest ) : void
    {
        if ( $quest->status !== 'draft' )
        {
            throw new \application\exceptions\ValidationException( 'Удалять можно только квесты в черновиках' );
        }

        if ( $quest->user_id !== new UserService( new \application\repositories\UserRepository )->getCurrentUser()?->id ?? 0 )
        {
            throw new \application\exceptions\ForbiddenException( 'Недостаточно прав для удаления чужих квестов' );
        }

        $this->_repository->delete( $quest->id );
    }

    /**
     * @throws \Krugozor\Database\MySqlException
     */
    public function approve( \application\models\Quest $quest ) : void
    {
        $quest->is_approved = true;
        $this->_repository->save( $quest );
    }

    /**
     * @throws \Krugozor\Database\MySqlException
     */
    public function reject( \application\models\Quest $quest ) : void
    {
        $this->_repository->delete( $quest->id );
    }

}
