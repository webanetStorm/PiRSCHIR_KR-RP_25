<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 16:22
 */

return array (
    ''              => array ( 'controller' => 'main', 'action' => 'index' ),
    'auth/login'    => array ( 'controller' => 'auth', 'action' => 'login' ),
    'auth/register' => array ( 'controller' => 'auth', 'action' => 'register' ),
    'auth/logout'   => array ( 'controller' => 'auth', 'action' => 'logout' ),
    'auth/profile'  => array ( 'controller' => 'auth', 'action' => 'profile' ),

    'quests'                  => array ( 'controller' => 'quests', 'action' => 'index' ),
    'quests/create'           => array ( 'controller' => 'quests', 'action' => 'create' ),
    'quests/my'               => array ( 'controller' => 'quests', 'action' => 'my' ),
    'quests/view/{id:\d+}'    => array ( 'controller' => 'quests', 'action' => 'view' ),
    'quests/publish/{id:\d+}' => array ( 'controller' => 'quests', 'action' => 'publish' ),
    'quests/update/{id:\d+}'  => array ( 'controller' => 'quests', 'action' => 'update' ),
    'quests/delete/{id:\d+}'  => array ( 'controller' => 'quests', 'action' => 'delete' ),

    'admin/moderate'         => array ( 'controller' => 'admin', 'action' => 'moderate' ),
    'admin/approve/{id:\d+}' => array ( 'controller' => 'admin', 'action' => 'approve' ),
    'admin/reject/{id:\d+}'  => array ( 'controller' => 'admin', 'action' => 'reject' ),

    'api/auth/login'    => array ( 'controller' => 'api/auth', 'action' => 'login' ),
    'api/auth/register' => array ( 'controller' => 'api/auth', 'action' => 'register' ),
    'api/auth/profile'  => array ( 'controller' => 'api/auth', 'action' => 'profile' ),

    'api/quests'                  => array ( 'controller' => 'api/quests', 'action' => 'index' ),
    'api/quests/{id:\d+}'         => array ( 'controller' => 'api/quests', 'action' => 'view' ),
    'api/quests/my'               => array ( 'controller' => 'api/quests', 'action' => 'my' ),
    'api/quests/create'           => array ( 'controller' => 'api/quests', 'action' => 'create' ),
    'api/quests/{id:\d+}/update'  => array ( 'controller' => 'api/quests', 'action' => 'update' ),
    'api/quests/{id:\d+}/delete'  => array ( 'controller' => 'api/quests', 'action' => 'delete' ),
    'api/quests/{id:\d+}/publish' => array ( 'controller' => 'api/quests', 'action' => 'publish' ),

    'api/admin'                  => array ( 'controller' => 'api/admin', 'action' => 'index' ),
    'api/admin/approve/{id:\d+}' => array ( 'controller' => 'api/admin', 'action' => 'approve' ),
    'api/admin/reject/{id:\d+}'  => array ( 'controller' => 'api/admin', 'action' => 'reject' ),
);
