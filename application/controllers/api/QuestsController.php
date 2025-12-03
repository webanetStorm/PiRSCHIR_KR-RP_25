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

    public function indexAction() : void
    {
        try
        {
            $this->checkAccess();

            $this->success( array_map( fn( $quest ) => $quest->toArray(), \application\models\Quest::getActive() ) );
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
            }

            if ( !( $quest = \application\models\Quest::findById( $id ) ) )
            {
                $this->notFound();
            }

            $isOwner = $this->user && $quest->user_id === $this->user->id;

            if ( $quest->status !== \application\models\Quest::STATUS_ACTIVE && !$isOwner )
            {
                $this->forbidden();
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
            $this->requireApiAuth();

            $this->success( array_map( fn( $quest ) => $quest->toArray(), \application\models\Quest::findByUserId( $this->user->id ) ) );

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
            $this->requireApiAuth();
            $this->checkAccess( 'create' );

            $data = $this->getJsonInput();

            if ( $errors = \application\services\QuestService::validateQuestData( $data ) )
            {
                $this->validationError( $errors );
            }

            $quest = \application\services\QuestService::createQuest( $data, $this->user );

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
            $this->requireApiAuth();

            if ( !( $quest = \application\models\Quest::findById( (int)( $this->route['id'] ?? 0 ) ) ) )
            {
                $this->notFound();
            }

            if ( $quest->user_id !== $this->user->id )
            {
                $this->forbidden();
            }

            $quest = \application\services\QuestService::updateQuest( $quest, $this->getJsonInput() );

            $this->success( $quest->toArray(), 'Квест успешно обновлен' );
        }
        catch ( \Exception $e )
        {
            $this->error( $e->getMessage(), [], 422 );
        }
    }

    public function deleteAction() : void
    {
        try
        {
            $this->requireApiAuth();

            $questId = (int)( $this->route['id'] ?? 0 );
            $quest = \application\models\Quest::findById( $questId );

            if ( !$quest )
            {
                $this->notFound();
            }

            if ( $quest->user_id !== $this->user->id )
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
            $this->requireApiAuth();

            if ( !( $quest = \application\models\Quest::findById( (int)( $this->route['id'] ?? 0 ) ) ) )
            {
                $this->notFound();
            }

            if ( $quest->user_id !== $this->user->id )
            {
                $this->forbidden();
            }

            $quest = \application\services\QuestService::publishQuest( $quest );

            $this->success( $quest->toArray(), 'Квест успешно опубликован' );
        }
        catch ( \Exception $e )
        {
            $this->error( $e->getMessage(), [], 422 );
        }
    }

}
