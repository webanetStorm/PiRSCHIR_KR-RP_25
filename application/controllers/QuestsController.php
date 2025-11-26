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

    public function createAction() : void
    {
        $this->checkAccess();

        if ( $_POST )
        {
            new \application\services\QuestService()->create( $this->getCurrentUserId(), $_POST );

            $this->view->redirect( '/quests/my' );
        }
        else
        {
            $this->view->render( 'Создать квест' );
        }
    }

}
