<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 01.12.2025
 * Time: 23:13
 */

/**
 * @var application\models\Quest $quest
 * @var application\models\User $user
 * @var bool $isOwner
 */
?>

<div class="quest-detail">
    <div class="quest-detail__header">
        <h1><?= htmlspecialchars( $quest->title ) ?></h1>
        <div class="quest-card__meta">
            <span class="badge badge--<?= $quest->type ?>"><?= $quest->type ?></span>
            <span class="badge badge--<?= $quest->status ?>"><?= $quest->status ?></span>
            <span class="quest-detail__reward">+<?= $quest->reward ?> XP</span>
        </div>
    </div>

    <div class="quest-detail__content">
        <h3>Описание</h3>
        <p><?= nl2br( htmlspecialchars( $quest->description ) ) ?></p>

        <?php if ( $quest->type === 'collective' && $quest->min_participants ): ?>
            <div class="quest-detail__info">
                <h4>Участники</h4>
                <p>Минимум: <?= $quest->min_participants ?> человек</p>
            </div>
        <?php endif ?>

        <?php if ( $quest->deadline ): ?>
            <div class="quest-detail__info">
                <h4>Дедлайн</h4>
                <p><?= date( 'd.m.Y H:i', strtotime( $quest->deadline ) ) ?></p>
            </div>
        <?php endif ?>
    </div>

    <div class="form-actions">
        <?php if ( $isOwner && $quest->status === 'draft' ): ?>
            <a href="/quests/publish/<?= $quest->id ?>" class="btn btn--success">Опубликовать</a>
        <?php endif ?>
        <a href="/quests" class="btn btn--secondary">Назад к списку</a>
    </div>
</div>
