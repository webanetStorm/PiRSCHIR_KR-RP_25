<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 22:28
 */

namespace application\controllers;


class QuestsController extends \application\core\Controller
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
    public function actionIndex() : void
    {
        $quests = $this->_questRepository->findActive();

        $this->view->render( 'Список квестов', compact( 'quests' ) );
    }

    /**
     * @throws \application\exceptions\NotFoundHttpException
     * @throws \Krugozor\Database\MySqlException
     */
    public function actionView() : void
    {
        if ( !( $quest = $this->_questRepository->findById( (int)( $this->route['id'] ?? 0 ) ) ) )
        {
            throw new \application\exceptions\NotFoundHttpException;
        }

        $user = $this->currentUser;
        $isOwner = $quest->user_id === $user?->id;

        $this->view->render( 'Просмотр квеста', compact( 'quest', 'user', 'isOwner' ) );
    }

    public function actionCreate() : void
    {
        $error = '';
        $success = false;

        if ( $_POST )
        {
            try
            {
                $this->_questService->create( $_POST, $this->currentUser );
                $success = true;
            }
            catch ( \application\exceptions\ValidationException $e )
            {
                $error = $e->getMessage();
            }
        }

        $this->view->render( 'Создание квеста', [
            'error'      => $error,
            'success'    => $success,
            'questTypes' => [
                \application\models\Quest::TYPE_INDIVIDUAL => 'Индивидуальный',
                \application\models\Quest::TYPE_COLLECTIVE => 'Коллективный',
                \application\models\Quest::TYPE_TIMED      => 'Ограниченный по времени'
            ]
        ] );
    }

    /**
     * @throws \Krugozor\Database\MySqlException
     * @throws \application\exceptions\NotFoundHttpException
     */
    public function actionUpdate() : void
    {
        if ( !( $quest = $this->_questRepository->findById( (int)( $this->route['id'] ?? 0 ) ) ) )
        {
            throw new \application\exceptions\NotFoundHttpException;
        }

        $error = '';
        $success = false;

        if ( $_POST )
        {
            try
            {
                $this->_questService->update( $quest, $_POST, $this->currentUser?->id ?? 0 );
                $success = true;
            }
            catch ( \Exception $e )
            {
                $error = $e->getMessage();
            }
        }

        $this->view->render( 'Редактирование квеста', [
            'quest'      => $quest,
            'error'      => $error,
            'success'    => $success,
            'questTypes' => [
                \application\models\Quest::TYPE_INDIVIDUAL => 'Индивидуальный',
                \application\models\Quest::TYPE_COLLECTIVE => 'Коллективный',
                \application\models\Quest::TYPE_TIMED      => 'Ограниченный по времени'
            ]
        ] );
    }

    /**
     * @throws \Krugozor\Database\MySqlException
     */
    public function actionMy() : void
    {
        $quests = $this->_questRepository->findByUserId( $this->currentUser?->id ?? 0 );

        $this->view->render( 'Мои квесты', compact( 'quests' ) );
    }

    /**
     * @throws \application\exceptions\NotFoundHttpException
     * @throws \application\exceptions\ForbiddenException
     * @throws \Krugozor\Database\MySqlException
     */
    public function actionPublish() : void
    {
        if ( !( $quest = $this->_questRepository->findById( (int)( $this->route['id'] ?? 0 ) ) ) )
        {
            throw new \application\exceptions\NotFoundHttpException;
        }

        if ( $quest->user_id !== $this->currentUser->id )
        {
            throw new \application\exceptions\ForbiddenException;
        }

        try
        {
            $this->_questService->publish( $quest );
        }
        catch ( \application\exceptions\ValidationException )
        {
        }

        $this->view->redirect( '/quests' );
    }

    /**
     * @throws \application\exceptions\NotFoundHttpException
     * @throws \application\exceptions\ForbiddenException
     * @throws \Krugozor\Database\MySqlException
     */
    public function actionDelete() : void
    {
        if ( !( $quest = $this->_questRepository->findById( (int)( $this->route['id'] ?? 0 ) ) ) )
        {
            throw new \application\exceptions\NotFoundHttpException;
        }

        if ( $quest->user_id !== $this->currentUser->id )
        {
            throw new \application\exceptions\ForbiddenException;
        }

        $this->_questService->delete( $quest );

        $this->view->redirect( '/quests/my' );
    }

}
