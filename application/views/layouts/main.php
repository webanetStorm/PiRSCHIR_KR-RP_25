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
 */
?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title ?></title>
        <link rel="stylesheet" href="/public/styles/global.css">
        <link rel="stylesheet" href="/public/styles/auth.css">
    </head>
    <body>
        <div class="content">
            <div class="content__main">
                <header class="content__header">
                    <div class="content__block block block_header header">
                        <div class="header__top top">
                            <h2 class="top__site-name"><?= APP_NAME ?></h2>
                            <div class="top__user">
                                <?php if ( \application\models\User::isAuthorized() ): ?>
                                    <div class="user-menu">
                                        <div class="user-menu__avatar"><?= mb_substr( $_SESSION['user_name'], 0, 1 ) ?></div>
                                        <span class="user-menu__name"><?= htmlspecialchars( $_SESSION['user_name'] ) ?></span>
                                        <div class="user-menu__dropdown">
                                            <a href="/auth/profile" class="user-menu__link">👤 Профиль</a>
                                            <a href="/quests/create" class="user-menu__link">🎯 Создать квест</a>
                                            <a href="/auth/logout" class="user-menu__link user-menu__link--logout">🚪 Выйти</a>
                                        </div>
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
                                <li class="menu__item"><a class="menu__link" href="/">Главная</a></li>
                            </ul>
                            <ul class="menu__list">
                                <li class="menu__item"><a class="menu__link" href="#">Модерация</a></li>
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
    </body>
</html>
