<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 02.12.2025
 * Time: 23:03
 */

namespace application\exceptions;


class DomainException extends \Exception
{

    public function __construct( string $message = 'Внутренняя ошибка сервера', int $code = 500 )
    {
        parent::__construct( $message, $code );
    }

}
