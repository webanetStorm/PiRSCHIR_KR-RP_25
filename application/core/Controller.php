<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 16:34
 */

namespace application\core;


abstract class Controller
{

    protected array $route;

    protected View $view;

    protected \application\services\UserService $userService;

    protected ?\application\models\User $currentUser;


    /**
     * @throws \application\exceptions\UnauthorizedException
     * @throws \Krugozor\Database\MySqlException
     * @throws \application\exceptions\DomainException
     */
    public function __construct( array $route )
    {
        $this->route = $route;
        $this->view = new View( $route );

        $this->userService = new \application\services\UserService( new \application\repositories\UserRepository );
        $this->currentUser = $this->userService->getCurrentUser();

        new \application\services\AccessService( $this->currentUser?->role ?? 'guest' )->check( $this->route['controller'], $this->route['action'] ?? 'index' );
    }

}
