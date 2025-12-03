<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 22:28
 */

namespace application\controllers;


use JetBrains\PhpStorm\NoReturn;


class QuestsController extends \application\core\Controller
{

    public function indexAction() : void
    {
        $quests = \application\models\Quest::getActive();

        $this->view->render( 'Список квестов', compact( 'quests' ) );
    }

    public function createAction() : void
    {
        $this->checkAccess();
        $error = '';
        $success = false;

        if ( $_POST )
        {
            try
            {
                \application\services\QuestService::createQuest( $_POST, \application\services\UserService::getCurrentUser()->id );
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

    public function updateAction() : void
    {
        $this->checkAccess();

        $quest = \application\models\Quest::findById( (int)( $this->route['id'] ?? 0 ) );

        if ( $quest->user_id !== \application\services\UserService::getCurrentUser()->id )
        {
            $this->view->errorCode( 403 );
        }

        $error = '';
        $success = false;

        if ( $_POST )
        {
            try
            {
                \application\services\QuestService::updateQuest( $quest, $_POST );
                $success = true;
            }
            catch ( \application\exceptions\ValidationException $e )
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

    public function viewAction() : void
    {
        if ( !( $quest = \application\models\Quest::findById( (int)( $this->route['id'] ?? 0 ) ) ) )
        {
            $this->view->errorCode( 404 );
        }

        $user = \application\services\UserService::getCurrentUser();
        $isOwner = $quest->user_id === $user?->id;

        $this->view->render( 'Просмотр квеста', compact( 'quest', 'user', 'isOwner' ) );
    }

    public function myAction() : void
    {
        $this->checkAccess();

        $quests = \application\models\Quest::findByUserId( \application\services\UserService::getCurrentUser()->id );

        $this->view->render( 'Мои квесты', compact( 'quests' ) );
    }

    #[NoReturn]
    public function publishAction() : void
    {
        $this->checkAccess();

        $quest = \application\models\Quest::findById( (int)( $this->route['id'] ?? 0 ) );

        if ( !$quest || $quest->user_id !== \application\services\UserService::getCurrentUser()->id )
        {
            $this->view->errorCode( 403 );
        }

        try
        {
            \application\services\QuestService::publishQuest( $quest );
            $this->view->redirect( '/quests' );
        }
        catch ( \application\exceptions\ValidationException $e )
        {
            $this->view->errorCode( 400 );
        }
    }

    #[NoReturn]
    public function deleteAction() : void
    {
        $this->checkAccess();

        $quest = \application\models\Quest::findById( (int)( $this->route['id'] ?? 0 ) );

        if ( !$quest || $quest->user_id !== \application\services\UserService::getCurrentUser()->id )
        {
            $this->view->errorCode( 403 );
        }

        \application\services\QuestService::deleteQuest( $quest );
        $this->view->redirect( '/quests/my' );
    }

}
