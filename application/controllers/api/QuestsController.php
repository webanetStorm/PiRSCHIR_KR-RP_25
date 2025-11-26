<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 22:30
 */

namespace application\controllers\api;


class QuestsController extends \application\core\Controller
{

    public function createAction() : void
    {
        $this->checkAccess();

        $data = json_decode( file_get_contents( 'php://input' ), true );
        $userId = $this->getCurrentUserId();

        try
        {
            $quest = new \application\services\QuestService()->create( $userId, $data );

            http_response_code( 201 );

            echo json_encode( [ 'data' => $quest ] );
        }
        catch ( \Exception $e )
        {
            http_response_code( 400 );

            echo json_encode( [ 'error' => $e->getMessage() ] );
        }
    }

}
