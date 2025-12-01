<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 22:40
 */

namespace application\exceptions;


class ValidationException extends \Exception
{

    public function __construct( string $message = 'Ошибка валидации', int $code = 400 )
    {
        parent::__construct( $message, $code );
    }

}
