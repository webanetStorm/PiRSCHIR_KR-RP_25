<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 02.12.2025
 * Time: 0:00
 */

/**
 * @var application\models\Quest $quest
 * @var string $error
 * @var bool $success
 * @var array $questTypes
 */
?>

<?php if ( $success ): ?>
    <div class="alert alert--success">
        ✅ Квест «<?= htmlspecialchars( $quest->title ) ?>» обновлён.
    </div>
<?php endif ?>

<?php if ( $error ): ?>
    <div class="alert alert--error">
        ❌ <?= htmlspecialchars( $error ) ?>
    </div>
<?php endif ?>

<form method="POST" class="quest-form">
    <div class="form-group">
        <label for="title">Название *</label>
        <input type="text" id="title" name="title" class="form-control" required maxlength="255" value="<?= htmlspecialchars( $quest->title ) ?>">
    </div>

    <div class="form-group">
        <label for="description">Описание *</label>
        <textarea id="description" name="description" class="form-control" required rows="4"><?= htmlspecialchars( $quest->description ) ?></textarea>
    </div>

    <div class="form-group">
        <label>Тип квеста *</label>
        <div class="radio-group">
            <?php foreach ( $questTypes as $value => $label ): ?>
                <label class="radio-item">
                    <input type="radio" name="type" value="<?= $value ?>"
                        <?= $quest->type === $value ? 'checked' : '' ?>>
                    <span><?= htmlspecialchars( $label ) ?></span>
                </label>
            <?php endforeach ?>
        </div>
    </div>

    <div class="form-group">
        <label for="reward">Награда (XP)</label>
        <input type="number" id="reward" name="reward" class="form-control" min="1" max="1000" value="<?= $quest->reward ?>">
    </div>

    <div class="form-group <?= $quest->type !== \application\models\Quest::TYPE_COLLECTIVE ? 'hidden' : '' ?>"
         id="minParticipantsGroup">
        <label for="min_participants">Минимум участников (от 2)</label>
        <input type="number" id="min_participants" name="min_participants" class="form-control" min="2" value="<?= $quest->min_participants ?>">
    </div>

    <div class="form-group <?= $quest->type !== \application\models\Quest::TYPE_TIMED ? 'hidden' : '' ?>"
         id="deadlineGroup">
        <label for="deadline">Дедлайн</label>
        <input type="datetime-local" id="deadline" name="deadline" class="form-control" value="<?= $quest->deadline ? date( 'Y-m-d\TH:i', strtotime( $quest->deadline ) ) : '' ?>">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Сохранить</button>
        <a href="/quests/my" class="btn btn--secondary">Отмена</a>
    </div>
</form>
