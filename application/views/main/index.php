<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 26.11.2025
 * Time: 16:47
 */

/** @var bool $isLoggedIn */
?>

<div class="home">
    <div class="home__hero hero">
        <div class="hero__content">
            <h1 class="hero__title">Quelyd — Платформа пользовательских квестов</h1>
            <p class="hero__subtitle">Создавайте, выполняйте и получайте опыт в уникальных заданиях</p>

            <div class="hero__actions">
                <?php if ( $isLoggedIn ): ?>
                    <a href="/quests/create" class="button button--primary button--large">🎯 Создать квест</a>
                    <a href="/quests" class="button button--secondary button--large">📋 Смотреть квесты</a>
                <?php else: ?>
                    <a href="/auth/register" class="button button--primary button--large">🚀 Начать играть</a>
                    <a href="/quests" class="button button--secondary button--large">👀 Посмотреть квесты</a>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
