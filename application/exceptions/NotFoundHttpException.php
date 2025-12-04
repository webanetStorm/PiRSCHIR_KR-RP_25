<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 04.12.2025
 * Time: 18:46
 */

namespace application\exceptions;


class NotFoundHttpException extends \Exception
{

    public function __construct( string $message = 'Страница не найдена', int $code = 404 )
    {
        parent::__construct( $message, $code );
    }

}
