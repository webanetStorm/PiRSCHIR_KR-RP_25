<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 04.12.2025
 * Time: 23:11
 */

namespace application\controllers;


class AdminController extends \application\core\Controller
{

    private \application\repositories\QuestRepository $_questRepository;

    private \application\services\QuestService $_questService;


    public function __construct( array $route )
    {
        parent::__construct( $route );

        $this->_questService = new \application\services\QuestService( $this->_questRepository = new \application\repositories\QuestRepository );
    }

    /**
     * @throws \Krugozor\Database\MySqlException
     */
    public function actionModerate() : void
    {
        $pendingQuests = $this->_questRepository->findPendingApproval();

        $this->view->render( 'Модерация квестов', compact( 'pendingQuests' ) );
    }

    public function actionApprove() : void
    {
        if ( !( $quest = $this->_questRepository->findById( (int)( $this->route['id'] ?? 0 ) ) ) )
        {
            throw new \application\exceptions\NotFoundHttpException;
        }

        try
        {
            $this->_questService->approve( $quest );

            $this->view->redirect( '/admin/moderate' );
        }
        catch ( \Exception $e )
        {
            $this->view->render( 'Ошибка', [ 'error' => $e->getMessage() ] );
        }
    }

    /**
     * @throws \application\exceptions\NotFoundHttpException
     * @throws \Krugozor\Database\MySqlException
     */
    public function actionReject() : void
    {
        if ( !( $quest = $this->_questRepository->findById( (int)( $this->route['id'] ?? 0 ) ) ) )
        {
            throw new \application\exceptions\NotFoundHttpException;
        }

        try
        {
            $this->_questService->reject( $quest );

            $this->view->redirect( '/admin/moderate' );
        }
        catch ( \Exception $e )
        {
            $this->view->render( 'Ошибка', [ 'error' => $e->getMessage() ] );
        }
    }

}
