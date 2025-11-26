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

    private \application\lib\DB $_db;


    public function __construct()
    {
        $this->_db = new \application\lib\DB;
    }

}
