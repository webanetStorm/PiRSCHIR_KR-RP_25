<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 16:10
 */

namespace application\core;


abstract class Model
{

    public function __construct( ?array $data = null )
    {
        if ( $data !== null )
        {
            $this->fill( $data );
        }
    }

    public function fill( array $data ) : void
    {
        foreach ( $data as $key => $value )
        {
            if ( property_exists( $this, $key ) )
            {
                $this->$key = $value;
            }
        }
    }

    abstract public function validate();

}
