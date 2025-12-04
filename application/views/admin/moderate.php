<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 04.12.2025
 * Time: 23:15
 */

/** @var array $pendingQuests */
?>

<h1>Модерация квестов</h1>

<?php if ( empty( $pendingQuests ) ): ?>
    <p>Нет квестов на модерации</p>
<?php else: ?>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>ID квеста</th>
                <th>Название</th>
                <th>ID автора</th>
                <th>Тип</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $pendingQuests as $quest ): ?>
                <tr>
                    <td><?= $quest->id ?></td>
                    <td><?= htmlspecialchars( $quest->title ) ?></td>
                    <td><?= htmlspecialchars( $quest->user_id ) ?></td>
                    <td><?= htmlspecialchars( $quest->type ) ?></td>
                    <td>
                        <form method="POST" action="/admin/approve/<?= $quest->id ?>" style="display: inline">
                            <button type="submit" onclick="return confirm('Одобрить квест?')">✅</button>
                        </form>
                        <form method="POST" action="/admin/reject/<?= $quest->id ?>" style="display: inline">
                            <button type="submit" onclick="return confirm('Удалить квест?')">❌</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?>

