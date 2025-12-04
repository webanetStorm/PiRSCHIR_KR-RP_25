<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 04.12.2025
 * Time: 21:35
 */

namespace application\core;


trait InstanceTrait
{

    protected static ?self $_instance = null;


    public static function i() : static
    {
        if ( static::$_instance instanceof static )
        {
            return static::$_instance;
        }

        return static::$_instance = new static;
    }

}
