<?php
require_once '../../config/database.php';
include '../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'specialist') {
    header('Location: /sites/andrey/pages/login.php');
    exit;
}

$specialist_id = $_SESSION['user_id'];
$company_id = $_SESSION['company_id'];

$stmt = $pdo->prepare("
    SELECT 
        tickets.*,
        categories.name AS category_name,
        users.full_name AS author_name
    FROM tickets
    LEFT JOIN categories ON tickets.category_id = categories.id
    LEFT JOIN users ON tickets.user_id = users.id
    WHERE tickets.company_id = ?
    ORDER BY tickets.created_at DESC
");
$stmt->execute([$company_id]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="section">
    <div class="container">
        <h1>Панель специалиста</h1>

        <p class="form-text">
            Здесь специалист видит заявки компании и может брать их в работу.
        </p>

        <div class="table-wrapper">
            <h2>Заявки компании</h2>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Тема</th>
                        <th>Автор</th>
                        <th>Категория</th>
                        <th>Приоритет</th>
                        <th>Статус</th>
                        <th>Дата</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td>#<?= $ticket['id'] ?></td>
                            <td><?= htmlspecialchars($ticket['title']) ?></td>
                            <td><?= htmlspecialchars($ticket['author_name']) ?></td>
                            <td><?= htmlspecialchars($ticket['category_name'] ?? 'Без категории') ?></td>
                            <td><?= htmlspecialchars($ticket['priority']) ?></td>
                            <td><?= htmlspecialchars($ticket['status']) ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($ticket['created_at'])) ?></td>
                            <td>
                                <a href="ticket.php?id=<?= $ticket['id'] ?>" class="table-link">
                                    Обработать
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($tickets)): ?>
                        <tr>
                            <td colspan="8">Заявок пока нет.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include '../../includes/footer.php'; ?>