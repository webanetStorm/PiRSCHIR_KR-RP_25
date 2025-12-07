<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 07.12.2025
 * Time: 21:25
 */

/**
 * @var int $code
 * @var string $message
 */
?>

<div class="error-page">
    <div class="error-page__content">
        <div class="error-page__icon">🚨</div>
        <h1 class="error-page__title">Ошибка <?= $code ?></h1>
        <p class="error-page__message"><?= $message ?></p>
        <div class="error-page__actions">
            <a href="/" class="button button--primary">На главную</a>
            <a href="javascript:location.reload()" class="button button--secondary">Попробовать снова</a>
        </div>
    </div>
</div>
