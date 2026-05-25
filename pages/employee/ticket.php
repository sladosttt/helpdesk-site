<?php
require_once 'config/database.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header('Location: /pages/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$company_id = $_SESSION['company_id'];
$ticket_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $pdo->prepare("
    SELECT 
        tickets.*,
        categories.name AS category_name,
        specialist.full_name AS specialist_name
    FROM tickets
    LEFT JOIN categories ON tickets.category_id = categories.id
    LEFT JOIN users AS specialist ON tickets.specialist_id = specialist.id
    WHERE tickets.id = ? 
    AND tickets.user_id = ? 
    AND tickets.company_id = ?
");
$stmt->execute([$ticket_id, $user_id, $company_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    die('Заявка не найдена.');
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
        <h1>Заявка #<?= $ticket['id'] ?></h1>

        <div class="ticket-card">
            <h2><?= htmlspecialchars($ticket['title']) ?></h2>

            <div class="ticket-info">
                <p><strong>Категория:</strong> <?= htmlspecialchars($ticket['category_name'] ?? 'Без категории') ?></p>
                <p><strong>Приоритет:</strong> <?= htmlspecialchars($ticket['priority']) ?></p>
                <p><strong>Статус:</strong> <?= htmlspecialchars($ticket['status']) ?></p>
                <p><strong>Специалист:</strong> <?= htmlspecialchars($ticket['specialist_name'] ?? 'Не назначен') ?></p>
                <p><strong>Дата создания:</strong> <?= date('d.m.Y H:i', strtotime($ticket['created_at'])) ?></p>
            </div>

            <div class="ticket-description">
                <h3>Описание проблемы</h3>
                <p><?= nl2br(htmlspecialchars($ticket['description'])) ?></p>
            </div>
        </div>

        <div class="comments-block">
            <h2>Комментарии</h2>

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
        <a href="/pages/dashboard.php" class="btn btn_dark">Назад</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>