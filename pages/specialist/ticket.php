<?php
require_once '../../config/database.php';
include '../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'specialist') {
    header('Location: /sites/andrey/pages/login.php');
    exit;
}

$specialist_id = $_SESSION['user_id'];
$company_id = $_SESSION['company_id'];
$ticket_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$error = '';
$success = '';

$stmt = $pdo->prepare("
    SELECT 
        tickets.*,
        categories.name AS category_name,
        users.full_name AS author_name,
        users.email AS author_email,
        specialist.full_name AS specialist_name
    FROM tickets
    LEFT JOIN categories ON tickets.category_id = categories.id
    LEFT JOIN users ON tickets.user_id = users.id
    LEFT JOIN users AS specialist ON tickets.specialist_id = specialist.id
    WHERE tickets.id = ?
    AND tickets.company_id = ?
");
$stmt->execute([$ticket_id, $company_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    die('Заявка не найдена.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $priority = $_POST['priority'];
    $comment = trim($_POST['comment']);

    if (!in_array($status, ['new', 'in_progress', 'waiting', 'closed'])) {
        $error = 'Некорректный статус.';
    } elseif (!in_array($priority, ['low', 'medium', 'high'])) {
        $error = 'Некорректный приоритет.';
    } else {
        $update = $pdo->prepare("
            UPDATE tickets
            SET status = ?, priority = ?, specialist_id = ?
            WHERE id = ? AND company_id = ?
        ");
        $update->execute([
            $status,
            $priority,
            $specialist_id,
            $ticket_id,
            $company_id
        ]);

        if (!empty($comment)) {
            $commentStmt = $pdo->prepare("
                INSERT INTO ticket_comments (ticket_id, user_id, comment)
                VALUES (?, ?, ?)
            ");
            $commentStmt->execute([$ticket_id, $specialist_id, $comment]);
        }

        header('Location: ticket.php?id=' . $ticket_id);
        exit;
    }
}

$commentsStmt = $pdo->prepare("
    SELECT ticket_comments.*, users.full_name, users.role
    FROM ticket_comments
    LEFT JOIN users ON ticket_comments.user_id = users.id
    WHERE ticket_comments.ticket_id = ?
    ORDER BY ticket_comments.created_at ASC
");
$commentsStmt->execute([$ticket_id]);
$comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="section">
    <div class="container">
        <h1>Обработка заявки #<?= $ticket['id'] ?></h1>

        <?php if ($error): ?>
            <div class="alert alert_error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="ticket-card">
            <h2><?= htmlspecialchars($ticket['title']) ?></h2>

            <div class="ticket-info">
                <p><strong>Автор:</strong> <?= htmlspecialchars($ticket['author_name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($ticket['author_email']) ?></p>
                <p><strong>Категория:</strong> <?= htmlspecialchars($ticket['category_name'] ?? 'Без категории') ?></p>
                <p><strong>Специалист:</strong> <?= htmlspecialchars($ticket['specialist_name'] ?? 'Не назначен') ?></p>
                <p><strong>Приоритет:</strong> <?= htmlspecialchars($ticket['priority']) ?></p>
                <p><strong>Статус:</strong> <?= htmlspecialchars($ticket['status']) ?></p>
                <p><strong>Дата создания:</strong> <?= date('d.m.Y H:i', strtotime($ticket['created_at'])) ?></p>
            </div>

            <div class="ticket-description">
                <h3>Описание проблемы</h3>
                <p><?= nl2br(htmlspecialchars($ticket['description'])) ?></p>
            </div>
        </div>

        <div class="form-wrapper">
            <h2>Обновить заявку</h2>

            <form method="POST" class="form">
                <div class="form-group">
                    <label>Статус</label>
                    <select name="status" required>
                        <option value="new" <?= $ticket['status'] === 'new' ? 'selected' : '' ?>>Новая</option>
                        <option value="in_progress" <?= $ticket['status'] === 'in_progress' ? 'selected' : '' ?>>В работе</option>
                        <option value="waiting" <?= $ticket['status'] === 'waiting' ? 'selected' : '' ?>>Ожидает ответа</option>
                        <option value="closed" <?= $ticket['status'] === 'closed' ? 'selected' : '' ?>>Закрыта</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Приоритет</label>
                    <select name="priority" required>
                        <option value="low" <?= $ticket['priority'] === 'low' ? 'selected' : '' ?>>Низкий</option>
                        <option value="medium" <?= $ticket['priority'] === 'medium' ? 'selected' : '' ?>>Средний</option>
                        <option value="high" <?= $ticket['priority'] === 'high' ? 'selected' : '' ?>>Высокий</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Комментарий специалиста</label>
                    <textarea 
                        name="comment" 
                        rows="5" 
                        placeholder="Напишите комментарий для пользователя"
                    ></textarea>
                </div>

                <button type="submit" class="btn">Сохранить изменения</button>
            </form>
        </div>

        <br>

        <div class="comments-block">
            <h2>История комментариев</h2>

            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <div class="comment__top">
                        <strong><?= htmlspecialchars($comment['full_name']) ?></strong>
                        <span><?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?></span>
                    </div>

                    <p><?= nl2br(htmlspecialchars($comment['comment'])) ?></p>
                </div>
            <?php endforeach; ?>

            <?php if (empty($comments)): ?>
                <p class="form-text">Комментариев пока нет.</p>
            <?php endif; ?>
        </div>

        <br>
        <a href="/sites/andrey/pages/dashboard.php" class="btn btn_dark">Назад</a>
    </div>
</section>

<?php include '../../includes/footer.php'; ?>