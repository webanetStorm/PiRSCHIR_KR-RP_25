<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 28.11.2025
 * Time: 20:32
 */

/** @var string $error */
?>

<div class="auth">
    <div class="auth__card card">
        <div class="card__header">
            <h1 class="card__title">🔐 Вход в Quelyd</h1>
            <p class="card__subtitle">Система пользовательских квестов</p>
        </div>
        <div class="card__body">
            <?php if ( $error ): ?>
                <div class="alert alert--error">
                    <div class="alert__icon">⚠️</div>
                    <div class="alert__content"><?= htmlspecialchars( $error ) ?></div>
                </div>
            <?php endif ?>
            <form class="form" method="post">
                <div class="form__group">
                    <label class="form__label" for="email">Email</label>
                    <input class="form__input" type="email" id="email" name="email" value="<?= htmlspecialchars( $_POST['email'] ?? '' ) ?>" required>
                </div>
                <div class="form__group">
                    <label class="form__label" for="password">Пароль</label>
                    <input class="form__input" type="password" id="password" name="password" required>
                </div>
                <button class="form__button button button--primary button--full" type="submit">Войти</button>
            </form>
            <div class="auth__footer">
                <p class="auth__text">Нет аккаунта? <a class="auth__link" href="/auth/register">Создайте его</a></p>
            </div>
        </div>
    </div>
</div>
