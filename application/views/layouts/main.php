<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 16:47
 */

/**
 * @var string $title
 * @var string $content
 * @var array $cssFiles
 */

$currentUser = new \application\services\UserService( new \application\repositories\UserRepository() )->getCurrentUser() ?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title ?></title>
        <?php foreach ( $cssFiles as $cssFile ): ?>
            <link rel="stylesheet" href="/public/styles/<?= htmlspecialchars( $cssFile ) ?>">
        <?php endforeach ?>
    </head>
    <body>
        <div class="content">
            <div class="content__main">
                <header class="content__header">
                    <div class="content__block block block_header header">
                        <div class="header__top top">
                            <h2 class="top__site-name"><?= APP_NAME ?></h2>
                            <div class="top__user">
                                <?php if ( $currentUser ): ?>
                                    <div class="user-menu">
                                        <div class="user-menu__avatar"><?= $currentUser->getAvatarLetters() ?></div>
                                        <a href="/auth/profile" class="user-menu__name"><?= $currentUser->name ?></a>
                                    </div>
                                <?php else: ?>
                                    <div class="auth-links">
                                        <a href="/auth/login" class="auth-links__link">Войти</a>
                                        <a href="/auth/register" class="auth-links__link auth-links__link--primary">Регистрация</a>
                                    </div>
                                <?php endif ?>
                            </div>
                        </div>
                        <div class="header__menu menu">
                            <ul class="menu__list">
                                <li class="menu__item">
                                    <a class="menu__link <?= ( $this->_route['controller'] ?? '' ) === 'main' ? 'menu__link--active' : '' ?>"href="/">🏠 Главная</a>
                                </li>
                                <li class="menu__item">
                                    <a class="menu__link <?= ( $this->_route['controller'] ?? '' ) === 'quests' && ( $this->_route['action'] ?? '' ) === 'index' ? 'menu__link--active' : '' ?>" href="/quests">📋 Все квесты</a>
                                </li>
                                <?php if ( $currentUser ): ?>
                                    <li class="menu__item">
                                        <a class="menu__link <?= ( $this->_route['controller'] ?? '' ) === 'quests' && ( $this->_route['action'] ?? '' ) === 'create' ? 'menu__link--active' : '' ?>" href="/quests/create">🎯 Создать квест</a>
                                    </li>
                                    <li class="menu__item">
                                        <a class="menu__link <?= ( $this->_route['controller'] ?? '' ) === 'quests' && ( $this->_route['action'] ?? '' ) === 'my' ? 'menu__link--active' : '' ?>" href="/quests/my">📁 Мои квесты</a>
                                    </li>
                                <?php endif ?>
                            </ul>
                            <ul class="menu__list">
                                <?php if ( $currentUser->role === 'admin' ): ?>
                                    <li class="menu__item">
                                        <a class="menu__link <?= ( $this->_route['controller'] ?? '' ) === 'admin' ? 'menu__link--active' : '' ?>" href="/admin/moderate">⚙️ Модерация</a>
                                    </li>
                                <?php endif ?>
                                <li class="menu__item">
                                    <a class="menu__link" href="/auth/logout">🚪 Выйти</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </header>
                <main class="content__main">
                    <?= $content ?>
                </main>
            </div>
            <footer class="content__footer footer">
                <span class="footer__copyright"><?= APP_NAME ?> — Система пользовательских квестов</span>
                <span class="footer__vk">@webanet</span>
            </footer>
        </div>
        <?php if ( $this->_route['controller'] === 'quests' ): ?>
            <script src="/public/scripts/quests.js"></script>
        <?php endif ?>
    </body>
</html>
