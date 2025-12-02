<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 28.11.2025
 * Time: 21:05
 */

/** @var array $quests */
?>

<div class="quests-header">
    <h1>🎯 Доступные квесты</h1>
    <?php if ( $isAuthorized = \application\services\UserService::isLoggedIn() ): ?>
        <a href="/quests/create" class="btn btn--primary">Создать квест</a>
    <?php endif ?>
</div>

<?php if ( empty( $quests ) ): ?>
    <div class="empty-message">
        <p>Пока нет доступных квестов</p>
        <?php if ( !$isAuthorized ): ?>
            <p><a href="/auth/register" class="btn btn--secondary">Зарегистрируйтесь</a>, чтобы создать первый квест!
            </p>
        <?php endif ?>
    </div>
<?php else: ?>
    <div class="quests-list">
        <?php foreach ( $quests as $quest ): ?>
            <div class="quest-card">
                <div class="quest-card__header">
                    <h3 class="quest-card__title"><?= htmlspecialchars( $quest->title ) ?></h3>
                    <span class="quest-card__reward">+<?= $quest->reward ?> XP</span>
                </div>
                <div class="quest-card__body">
                    <p class="quest-card__description"><?= htmlspecialchars( $quest->description ) ?></p>
                    <div class="quest-card__meta">
                        <span class="badge badge--<?= $quest->type ?>"><?= $quest->type ?></span>
                        <span class="badge badge--<?= $quest->status ?>"><?= $quest->status ?></span>
                    </div>
                </div>
                <div class="quest-card__actions">
                    <a href="/quests/view/<?= $quest->id ?>" class="btn btn--secondary">Подробнее</a>
                </div>
            </div>
        <?php endforeach ?>
    </div>
<?php endif ?>
