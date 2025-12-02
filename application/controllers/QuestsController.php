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
                $quest = \application\models\Quest::createByArray( [
                    'user_id'          => \application\services\UserService::getCurrentUser()->id,
                    'title'            => $_POST['title'] ?? '',
                    'description'      => $_POST['description'] ?? '',
                    'type'             => $_POST['type'] ?? \application\models\Quest::TYPE_INDIVIDUAL,
                    'reward'           => $_POST['reward'] ?? 20,
                    'min_participants' => $_POST['min_participants'] ?? 0,
                    'deadline'         => $_POST['deadline'] ?? null,
                    'status'           => \application\models\Quest::STATUS_DRAFT
                ] );

                $quest->save();
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

    public function viewAction() : void
    {
        if ( !( $id = (int)( $this->route['id'] ?? 0 ) ) )
        {
            $this->view->errorCode( 404 );
        }

        if ( !( $quest = \application\models\Quest::findById( $id ) ) )
        {
            $this->view->errorCode( 404 );
        }

        $user = \application\services\UserService::getCurrentUser();
        $isOwner = $quest->user_id === $user->id;

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

        if ( !( $id = (int)( $this->route['id'] ?? 0 ) ) )
        {
            $this->view->errorCode( 400 );
        }

        if ( !( $quest = \application\models\Quest::findById( $id ) ) || $quest->user_id !== \application\services\UserService::getCurrentUser()->id )
        {
            $this->view->errorCode( 403 );
        }

        if ( $quest->status !== \application\models\Quest::STATUS_DRAFT )
        {
            $this->view->errorCode( 400 );
        }

        $quest->status = \application\models\Quest::STATUS_ACTIVE;
        $quest->updated_at = time();
        $quest->save();

        $this->view->redirect( '/quests' );
    }

    public function updateAction() : void
    {
        $this->checkAccess();

        if ( !( $quest = \application\models\Quest::findById( (int)( $this->route['id'] ?? 0 ) ) ) || $quest->user_id !== \application\services\UserService::getCurrentUser()->id )
        {
            $this->view->errorCode( 403 );
        }

        $error = '';
        $success = false;

        if ( $_POST )
        {
            try
            {
                $quest->title = $_POST['title'] ?? $quest->title;
                $quest->description = $_POST['description'] ?? $quest->description;
                $quest->type = $_POST['type'] ?? $quest->type;
                $quest->reward = (int)( $_POST['reward'] ?? $quest->reward );
                $quest->min_participants = (int)( $_POST['min_participants'] ?? $quest->min_participants );
                $quest->deadline = !empty( $_POST['deadline'] ) ? $_POST['deadline'] : null;
                $quest->updated_at = time();

                $quest->validate();
                $quest->save();
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

    #[NoReturn]
    public function deleteAction() : void
    {
        $this->checkAccess();

        if ( !( $id = (int)( $this->route['id'] ?? 0 ) ) )
        {
            $this->view->errorCode( 400 );
        }

        if ( !( $quest = \application\models\Quest::findById( $id ) ) || $quest->user_id !== \application\services\UserService::getCurrentUser()->id )
        {
            $this->view->errorCode( 403 );
        }

        \application\models\Quest::deleteById( $id );

        $this->view->redirect( '/quests/my' );
    }

}
