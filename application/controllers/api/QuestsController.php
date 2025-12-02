<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 22:30
 */

namespace application\controllers\api;


class QuestsController extends ApiController
{

    public function listAction() : void
    {
        try
        {
            $this->checkAccess();

            $quests = \application\models\Quest::getActive();
            $data = array_map( fn( $quest ) => $quest->toArray(), $quests );

            $this->success( $data );
        }
        catch ( \Exception $e )
        {
            $this->error( $e->getMessage() );
        }
    }

    public function viewAction() : void
    {
        try
        {
            $this->checkAccess();

            if ( !( $id = (int)( $this->route['id'] ?? 0 ) ) )
            {
                $this->error( 'ID квеста не указан' );

                return;
            }

            if ( !( $quest = \application\models\Quest::findById( $id ) ) )
            {
                $this->notFound();

                return;
            }

            $currentUser = \application\services\UserService::getCurrentUser();
            $isOwner = $currentUser && $quest->user_id === $currentUser->id;

            if ( $quest->status !== \application\models\Quest::STATUS_ACTIVE && !$isOwner )
            {
                $this->forbidden();

                return;
            }

            $questData = $quest->toArray();
            $questData['is_owner'] = $isOwner;

            $this->success( $questData );

        }
        catch ( \Exception $e )
        {
            $this->error( $e->getMessage() );
        }
    }

    public function myAction() : void
    {
        try
        {
            $this->requireAuth();

            $quests = \application\models\Quest::findByUserId( \application\services\UserService::getCurrentUser()->id );

            $this->success( array_map( fn( $quest ) => $quest->toArray(), $quests ) );

        }
        catch ( \application\exceptions\UnauthorizedException $e )
        {
            $this->error( $e->getMessage(), [], 401 );
        }
        catch ( \Exception $e )
        {
            $this->error( $e->getMessage() );
        }
    }

    public function createAction() : void
    {
        try
        {
            $this->requireAuth();
            $this->checkAccess( 'create' );

            $data = $this->getJsonInput();

            if ( $errors = \application\services\QuestService::validateQuestData( $data ) )
            {
                $this->validationError( $errors );
            }

            $user = \application\services\UserService::getCurrentUser();
            $quest = \application\services\QuestService::createQuest( $data, $user );

            $this->success( $quest->toArray(), 'Квест успешно создан' );
        }
        catch ( \application\exceptions\UnauthorizedException $e )
        {
            $this->error( $e->getMessage(), [], 401 );
        }
        catch ( \application\exceptions\ValidationException $e )
        {
            $this->error( $e->getMessage(), [], 422 );
        }
        catch ( \Exception $e )
        {
            $this->error( 'Внутренняя ошибка сервера: ' . $e->getMessage(), [], 500 );
        }
    }

    public function updateAction() : void
    {
        try
        {
            $this->requireAuth();

            $questId = (int)( $this->route['id'] ?? 0 );
            $quest = \application\models\Quest::findById( $questId );

            if ( !$quest )
            {
                $this->notFound();
            }

            if ( $quest->user_id !== \application\services\UserService::getCurrentUser()->id )
            {
                $this->forbidden();
            }

            $data = $this->getJsonInput();
            $quest = \application\services\QuestService::updateQuest( $quest, $data );

            $this->success( $quest->toArray(), 'Квест успешно обновлен' );
        }
        catch ( \application\exceptions\ValidationException $e )
        {
            $this->error( $e->getMessage(), [], 422 );
        }
    }

    public function deleteAction() : void
    {
        try
        {
            $this->requireAuth();

            $questId = (int)( $this->route['id'] ?? 0 );
            $quest = \application\models\Quest::findById( $questId );

            if ( !$quest )
            {
                $this->notFound();
            }

            if ( $quest->user_id !== \application\services\UserService::getCurrentUser()->id )
            {
                $this->forbidden();
            }

            \application\services\QuestService::deleteQuest( $quest );

            $this->success( [], 'Квест успешно удален' );
        }
        catch ( \Exception $e )
        {
            $this->error( $e->getMessage() );
        }
    }

    public function publishAction() : void
    {
        try
        {
            $this->requireAuth();

            $questId = (int)( $this->route['id'] ?? 0 );
            $quest = \application\models\Quest::findById( $questId );

            if ( !$quest )
            {
                $this->notFound();
            }

            if ( $quest->user_id !== \application\services\UserService::getCurrentUser()->id )
            {
                $this->forbidden();
            }

            $quest = \application\services\QuestService::publishQuest( $quest );

            $this->success( $quest->toArray(), 'Квест успешно опубликован' );
        }
        catch ( \application\exceptions\ValidationException $e )
        {
            $this->error( $e->getMessage(), [], 422 );
        }
    }

}
