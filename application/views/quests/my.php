<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 01.12.2025
 * Time: 23:59
 */

/** @var array $quests */
?>

<div class="quests-header">
    <h1>Мои квесты</h1>
    <a href="/quests/create" class="btn btn--primary">Создать квест</a>
</div>

<?php if ( empty( $quests ) ): ?>
    <div class="empty-message">
        <p>У вас пока нет квестов</p>
        <p><a href="/quests/create" class="btn btn--secondary">Создать первый квест</a></p>
    </div>
<?php else: ?>
    <div class="quests-list">
        <?php foreach ( $quests as $quest ): ?>
            <div class="quest-card">
                <div class="quest-card__header">
                    <h3 class="quest-card__title"><?= htmlspecialchars( $quest->title ) ?></h3>
                    <span class="quest-card__reward">+<?= $quest->reward ?> XP</span>
                </div>
                <div class="quest-card__meta">
                    <span class="badge badge--<?= $quest->type ?>"><?= $quest->type ?></span>
                    <span class="badge badge--<?= $quest->status ?>"><?= $quest->status ?></span>
                </div>
                <div class="quest-card__actions">
                    <a href="/quests/view/<?= $quest->id ?>" class="btn btn--secondary">Подробнее</a>
                    <?php if ( $quest->status === 'draft' ): ?>
                        <a href="/quests/update/<?= $quest->id ?>" class="btn btn--primary">Редактировать</a>
                        <a href="/quests/delete/<?= $quest->id ?>"
                           class="btn btn--danger delete-link"
                           data-confirm="Удалить квест «<?= htmlspecialchars( $quest->title ) ?>»?">
                            Удалить
                        </a>
                    <?php endif ?>
                </div>
            </div>
        <?php endforeach ?>
    </div>
<?php endif ?>
