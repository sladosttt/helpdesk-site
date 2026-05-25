<?php
require_once 'config/database.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pages/login.php');
    exit;
}

$company_id = $_SESSION['company_id'];

$status = $_GET['status'] ?? '';
$priority = $_GET['priority'] ?? '';

$query = "
    SELECT 
        tickets.*,
        categories.name AS category_name,
        author.full_name AS author_name,
        specialist.full_name AS specialist_name
    FROM tickets
    LEFT JOIN categories ON tickets.category_id = categories.id
    LEFT JOIN users AS author ON tickets.user_id = author.id
    LEFT JOIN users AS specialist ON tickets.specialist_id = specialist.id
    WHERE tickets.company_id = ?
";

$params = [$company_id];

if (!empty($status)) {
    $query .= " AND tickets.status = ?";
    $params[] = $status;
}

if (!empty($priority)) {
    $query .= " AND tickets.priority = ?";
    $params[] = $priority;
}

$query .= " ORDER BY tickets.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="section">
    <div class="container">
        <h1>Все заявки компании</h1>

        <p class="form-text">
            Здесь администратор может просматривать все обращения сотрудников компании.
        </p>

        <form method="GET" class="filters">
            <div class="form-group">
                <label>Статус</label>
                <select name="status">
                    <option value="">Все статусы</option>
                    <option value="new" <?= $status === 'new' ? 'selected' : '' ?>>Новая</option>
                    <option value="in_progress" <?= $status === 'in_progress' ? 'selected' : '' ?>>В работе</option>
                    <option value="waiting" <?= $status === 'waiting' ? 'selected' : '' ?>>Ожидает ответа</option>
                    <option value="closed" <?= $status === 'closed' ? 'selected' : '' ?>>Закрыта</option>
                </select>
            </div>

            <div class="form-group">
                <label>Приоритет</label>
                <select name="priority">
                    <option value="">Все приоритеты</option>
                    <option value="low" <?= $priority === 'low' ? 'selected' : '' ?>>Низкий</option>
                    <option value="medium" <?= $priority === 'medium' ? 'selected' : '' ?>>Средний</option>
                    <option value="high" <?= $priority === 'high' ? 'selected' : '' ?>>Высокий</option>
                </select>
            </div>

            <button type="submit" class="btn">Применить</button>
            <a href="tickets.php" class="btn btn_dark">Сбросить</a>
        </form>

        <br>

        <div class="table-wrapper">
            <h2>Список заявок</h2>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Тема</th>
                        <th>Автор</th>
                        <th>Категория</th>
                        <th>Специалист</th>
                        <th>Приоритет</th>
                        <th>Статус</th>
                        <th>Дата</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td>#<?= $ticket['id'] ?></td>
                            <td><?= htmlspecialchars($ticket['title']) ?></td>
                            <td><?= htmlspecialchars($ticket['author_name'] ?? 'Неизвестно') ?></td>
                            <td><?= htmlspecialchars($ticket['category_name'] ?? 'Без категории') ?></td>
                            <td><?= htmlspecialchars($ticket['specialist_name'] ?? 'Не назначен') ?></td>
                            <td><?= htmlspecialchars($ticket['priority']) ?></td>
                            <td><?= htmlspecialchars($ticket['status']) ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($ticket['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($tickets)): ?>
                        <tr>
                            <td colspan="8">Заявки не найдены.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <br>
        <a href="/pages/dashboard.php" class="btn btn_dark">Назад</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>