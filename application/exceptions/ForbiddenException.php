<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 04.12.2025
 * Time: 1:44
 */

namespace application\exceptions;


class ForbiddenException extends \Exception
{

    public function __construct( string $message = 'Нет доступа', int $code = 403 )
    {
        parent::__construct( $message, $code );
    }

}
