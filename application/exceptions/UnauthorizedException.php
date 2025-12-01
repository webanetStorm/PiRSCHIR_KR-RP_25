<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 22:22
 */

namespace application\exceptions;


class UnauthorizedException extends \Exception
{

    public function __construct( string $message = 'Требуется авторизация', int $code = 401 )
    {
        parent::__construct( $message, $code );
    }

}
