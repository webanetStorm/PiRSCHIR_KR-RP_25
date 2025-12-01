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

    public function indexAction() : void
    {
        $quests = \application\models\Quest::getActive();

        $this->view->render( 'Список квестов', [
            'quests' => $quests
        ] );
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
                    'user_id'          => $this->getCurrentUserId(),
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
        $id = $_GET['id'] ?? 0;

        if ( !$id )
        {
            $this->view->errorCode( 404 );

            return;
        }

        $quest = \application\models\Quest::findById( (int)$id );

        if ( !$quest )
        {
            $this->view->errorCode( 404 );

            return;
        }

        $this->view->render( 'Просмотр квеста', [
            'quest' => $quest->toArray()
        ] );
    }

}
