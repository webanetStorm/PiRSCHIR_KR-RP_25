<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 28.11.2025
 * Time: 21:05
 */

/** @var array $quests */
?>

<div class="quests">
    <div class="quests__header">
        <h1 class="quests__title">🎯 Доступные квесты</h1>
        <?php if ( $isAuthorized = \application\models\User::isAuthorized() ): ?>
            <a href="/quests/create" class="button button--primary">Создать квест</a>
        <?php endif ?>
    </div>

    <div class="quests__list">
        <?php if ( !empty( $quests ) ): ?>
            <?php foreach ( $quests as $quest ): ?>
                <div class="quest-card">
                    <div class="quest-card__header">
                        <h3 class="quest-card__title"><?= htmlspecialchars( $quest['title'] ) ?></h3>
                        <span class="quest-card__reward">+<?= $quest['reward'] ?> XP</span>
                    </div>
                    <div class="quest-card__body">
                        <p class="quest-card__description"><?= htmlspecialchars( $quest['description'] ) ?></p>
                        <div class="quest-card__meta">
                            <span class="quest-card__type"><?= $quest['type'] ?></span>
                            <span class="quest-card__status quest-card__status--<?= $quest['status'] ?>">
                                <?= $quest['status'] ?>
                            </span>
                        </div>
                    </div>
                    <div class="quest-card__footer">
                        <a href="/quests/view?id=<?= $quest['id'] ?>" class="button button--secondary">Подробнее</a>
                    </div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <div class="quests__empty">
                <p>Пока нет доступных квестов</p>
                <?php if ( !$isAuthorized ): ?>
                    <p><a href="/auth/register">Зарегистрируйтесь</a>, чтобы создать первый квест!</p>
                <?php endif ?>
            </div>
        <?php endif ?>
    </div>
</div>
