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

    private \application\repositories\QuestRepository $_questRepository;

    private \application\services\QuestService $_questService;


    public function __construct( array $route )
    {
        parent::__construct( $route );

        $this->_questService = new \application\services\QuestService( $this->_questRepository = new \application\repositories\QuestRepository );
    }

    public function actionIndex() : void
    {
        try
        {
            $this->checkAccess();

            $this->success( array_map( fn( $quest ) => $quest->toArray(), $this->_questRepository->findActive() ) );
        }
        catch ( \Exception $e )
        {
            $this->error( $e->getMessage(), $e->getCode() );
        }
    }

    public function actionView() : void
    {
        try
        {
            $this->checkAccess();

            if ( !( $quest = $this->_questRepository->findById( (int)( $this->route['id'] ?? 0 ) ) ) )
            {
                $this->notFound();
            }

            $questData = $quest->toArray();
            $questData['is_owner'] = $quest->user_id === $this->currentUser?->id;

            $this->success( $questData );

        }
        catch ( \Exception $e )
        {
            $this->error( $e->getMessage(), $e->getCode() );
        }
    }

    public function actionMy() : void
    {
        try
        {
            $this->checkAccess();

            $this->success( array_map( fn( $quest ) => $quest->toArray(), $this->_questRepository->findByUserId( $this->currentUser->id ) ) );
        }
        catch ( \Exception $e )
        {
            $this->error( $e->getMessage(), $e->getCode() );
        }
    }

    public function actionCreate() : void
    {
        try
        {
            $this->checkAccess();

            $quest = $this->_questService->create( $this->getJsonInput(), $this->currentUser );

            $this->success( $quest->toArray(), 'Квест успешно создан' );
        }
        catch ( \Exception $e )
        {
            $this->error( $e->getMessage(), $e->getCode() );
        }
    }

    public function actionUpdate() : void
    {
        try
        {
            $this->checkAccess();

            if ( !( $quest = $this->_questRepository->findById( (int)( $this->route['id'] ?? 0 ) ) ) )
            {
                $this->notFound();
            }

            $quest = $this->_questService->update( $quest, $this->getJsonInput(), $this->currentUser?->id ?? 0 );

            $this->success( $quest->toArray(), 'Квест успешно обновлен' );
        }
        catch ( \Exception $e )
        {
            $this->error( $e->getMessage(), $e->getCode() );
        }
    }

    public function actionDelete() : void
    {
        try
        {
            $this->checkAccess();

            if ( !( $quest = $this->_questRepository->findById( (int)( $this->route['id'] ?? 0 ) ) ) )
            {
                $this->notFound();
            }

            $this->_questService->delete( $quest );

            $this->success( $quest->toArray(), 'Квест успешно удален' );
        }
        catch ( \Exception $e )
        {
            $this->error( $e->getMessage(), $e->getCode() );
        }
    }

    public function actionPublish() : void
    {
        try
        {
            $this->checkAccess();

            if ( !( $quest = $this->_questRepository->findById( (int)( $this->route['id'] ?? 0 ) ) ) )
            {
                $this->notFound();
            }

            $quest = $this->_questService->publish( $quest );

            $this->success( $quest->toArray(), 'Квест успешно опубликован' );
        }
        catch ( \Exception $e )
        {
            $this->error( $e->getMessage(), $e->getCode() );
        }
    }

}
