<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 16:43
 */

return array (
    'main.index'    => array ( 'guest', 'user', 'admin' ),
    'auth.login'    => array ( 'guest' ),
    'auth.register' => array ( 'guest' ),
    'auth.logout'   => array ( 'user', 'admin' ),
    'auth.profile'  => array ( 'user', 'admin' ),

    'quests.index'   => array ( 'guest', 'user', 'admin' ),
    'quests.view'    => array ( 'guest', 'user', 'admin' ),
    'quests.my'      => array ( 'user', 'admin' ),
    'quests.create'  => array ( 'user', 'admin' ),
    'quests.update'  => array ( 'user', 'admin' ),
    'quests.publish' => array ( 'user', 'admin' ),
    'quests.delete'  => array ( 'user', 'admin' ),
    'quests.join'    => array ( 'user', 'admin' ),

    'api/auth.login'    => array ( 'guest', 'user', 'admin' ),
    'api/auth.register' => array ( 'guest', 'user', 'admin' ),
    'api/auth.profile'  => array ( 'user', 'admin' ),

    'api/quests.index'   => array ( 'guest', 'user', 'admin' ),
    'api/quests.view'    => array ( 'guest', 'user', 'admin' ),
    'api/quests.my'      => array ( 'user', 'admin' ),
    'api/quests.create'  => array ( 'user', 'admin' ),
    'api/quests.update'  => array ( 'user', 'admin' ),
    'api/quests.delete'  => array ( 'user', 'admin' ),
    'api/quests.publish' => array ( 'user', 'admin' ),
);
