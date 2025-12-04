<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 04.12.2025
 * Time: 23:20
 */

namespace application\controllers\api;


use JetBrains\PhpStorm\NoReturn;


class AdminController extends ApiController
{

    private \application\repositories\QuestRepository $_questRepository;

    private \application\services\QuestService $_questService;


    public function __construct( array $route )
    {
        parent::__construct( $route );

        $this->_questService = new \application\services\QuestService( $this->_questRepository = new \application\repositories\QuestRepository );
    }

    #[NoReturn]
    public function actionIndex() : void
    {
        try
        {
            $this->checkAccess();

            $this->success( array_map( fn( $q ) => $q->toArray(), new \application\repositories\QuestRepository()->findPendingApproval() ) );
        }
        catch ( \Exception $e )
        {
            $this->error( $e->getMessage(), $e->getCode() );
        }
    }

    public function actionApprove() : void
    {
        try
        {
            $this->checkAccess();

            if ( !( $quest = $this->_questRepository->findById( (int)( $this->route['id'] ?? 0 ) ) ) )
            {
                $this->notFound();
            }

            $this->_questService->approve( $quest );

            $this->success( $quest->toArray(), 'Квест одобрен' );
        }
        catch ( \Exception $e )
        {
            $this->error( $e->getMessage(), $e->getCode() );
        }
    }

    public function actionReject() : void
    {
        try
        {
            $this->checkAccess();

            if ( !( $quest = $this->_questRepository->findById( (int)( $this->route['id'] ?? 0 ) ) ) )
            {
                $this->notFound();
            }

            $this->_questService->reject( $quest );

            $this->success( $quest->toArray(), 'Квест удалён' );
        }
        catch ( \Exception $e )
        {
            $this->error( $e->getMessage(), $e->getCode() );
        }
    }

}
