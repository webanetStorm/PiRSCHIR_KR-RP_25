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
    </head>
    <body>
        <div class="content">
            <div class="content__main">
                <header class="content__header">
                    <div class="content__block block block_header header">
                        <div class="header__top top">
                            <h2 class="top__site-name"><?= APP_NAME ?></h2>
                            <div class="top__user">
                                <span class="top__avatar">МБ</span>
                                <span class="top__user-name">Матвей</span>
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
