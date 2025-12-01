<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 28.11.2025
 * Time: 20:33
 */

/** @var string $error */
?>

<div class="auth">
    <div class="auth__card card">
        <div class="card__header">
            <h1 class="card__title">🎮 Присоединяйтесь к Quelyd</h1>
            <p class="card__subtitle">Создавайте квесты и зарабатывайте опыт</p>
        </div>
        <div class="card__body">
            <?php if ( $error ): ?>
                <div class="alert alert--error">
                    <div class="alert__icon">⚠️</div>
                    <div class="alert__content"><?= htmlspecialchars( $error ) ?></div>
                </div>
            <?php endif;?>
            <form class="form" method="post">
                <div class="form__group">
                    <label class="form__label" for="name">Имя игрока</label>
                    <input class="form__input" type="text" id="name" name="name" value="<?= htmlspecialchars( $_POST['name'] ?? '' ) ?>" required>
                    <div class="form__hint">Так вас будут видеть другие игроки</div>
                </div>
                <div class="form__group">
                    <label class="form__label" for="email">Email</label>
                    <input class="form__input" type="email" id="email" name="email" value="<?= htmlspecialchars( $_POST['email'] ?? '' ) ?>" required>
                </div>
                <div class="form__group">
                    <label class="form__label" for="password">Пароль</label>
                    <input class="form__input" type="password" id="password" name="password" required>
                    <div class="form__hint">Не менее 6 символов</div>
                </div>
                <button class="form__button button button--primary button--full" type="submit">Создать аккаунт</button>
            </form>
            <div class="auth__footer">
                <p class="auth__text">Уже есть аккаунт? <a class="auth__link" href="/auth/login">Войдите</a></p>
            </div>
        </div>
    </div>
</div>
