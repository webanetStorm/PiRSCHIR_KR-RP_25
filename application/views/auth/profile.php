<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 28.11.2025
 * Time: 20:34
 */

/** @var application\models\User $user */
?>

<div class="profile">
    <div class="profile__card card">
        <div class="card__header">
            <h1 class="card__title">👤 Профиль игрока</h1>
        </div>
        <div class="card__body">
            <div class="profile__info">
                <div class="profile__avatar">
                    <div class="avatar"><?= $user->getAvatarLetters() ?></div>
                </div>
                <div class="profile__details">
                    <div class="profile__field">
                        <span class="profile__label">Имя</span>
                        <span class="profile__value"><?= htmlspecialchars( $user->name ) ?></span>
                    </div>
                    <div class="profile__field">
                        <span class="profile__label">Почта</span>
                        <span class="profile__value"><?= htmlspecialchars( $user->email ) ?></span>
                    </div>
                    <div class="profile__field">
                        <span class="profile__label">Роль</span>
                        <span class="profile__value profile__role profile__role--<?= $user->role ?>">
                            <?= $user->role === 'admin' ? '👑 Администратор' : '🎮 Пользователь' ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="profile__actions">
                <a href="/quests/create" class="button button--primary">Создать квест</a>
                <a href="/auth/logout" class="button button--secondary">Выйти</a>
            </div>
        </div>
    </div>
</div>
