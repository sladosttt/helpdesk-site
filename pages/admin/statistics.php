<?php
require_once '../../config/database.php';
include '../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /sites/andrey/pages/login.php');
    exit;
}

$company_id = $_SESSION['company_id'];

$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE company_id = ?");
$totalStmt->execute([$company_id]);
$total = $totalStmt->fetchColumn();

$newStmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE company_id = ? AND status = 'new'");
$newStmt->execute([$company_id]);
$new = $newStmt->fetchColumn();

$progressStmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE company_id = ? AND status = 'in_progress'");
$progressStmt->execute([$company_id]);
$in_progress = $progressStmt->fetchColumn();

$waitingStmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE company_id = ? AND status = 'waiting'");
$waitingStmt->execute([$company_id]);
$waiting = $waitingStmt->fetchColumn();

$closedStmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE company_id = ? AND status = 'closed'");
$closedStmt->execute([$company_id]);
$closed = $closedStmt->fetchColumn();

$categoryStmt = $pdo->prepare("
    SELECT categories.name, COUNT(tickets.id) AS total
    FROM categories
    LEFT JOIN tickets ON categories.id = tickets.category_id
    WHERE categories.company_id = ?
    GROUP BY categories.id
    ORDER BY total DESC
");
$categoryStmt->execute([$company_id]);
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

$specialistStmt = $pdo->prepare("
    SELECT users.full_name, COUNT(tickets.id) AS total
    FROM users
    LEFT JOIN tickets ON users.id = tickets.specialist_id
    WHERE users.company_id = ? AND users.role = 'specialist'
    GROUP BY users.id
    ORDER BY total DESC
");
$specialistStmt->execute([$company_id]);
$specialists = $specialistStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="section">
    <div class="container">
        <h1>Статистика компании</h1>

        <p class="form-text">
            Аналитика по заявкам, статусам, категориям и специалистам.
        </p>

        <div class="stats-grid">
            <div class="stat-card">
                <span><?= $total ?></span>
                <p>Всего заявок</p>
            </div>

            <div class="stat-card">
                <span><?= $new ?></span>
                <p>Новые</p>
            </div>

            <div class="stat-card">
                <span><?= $in_progress ?></span>
                <p>В работе</p>
            </div>

            <div class="stat-card">
                <span><?= $waiting ?></span>
                <p>Ожидают ответа</p>
            </div>

            <div class="stat-card">
                <span><?= $closed ?></span>
                <p>Закрытые</p>
            </div>
        </div>

        <div class="grid-two">
            <div class="table-wrapper">
                <h2>Заявки по категориям</h2>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Категория</th>
                            <th>Количество</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= htmlspecialchars($category['name']) ?></td>
                                <td><?= $category['total'] ?></td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="2">Нет данных.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-wrapper">
                <h2>Заявки по специалистам</h2>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Специалист</th>
                            <th>Количество</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($specialists as $specialist): ?>
                            <tr>
                                <td><?= htmlspecialchars($specialist['full_name']) ?></td>
                                <td><?= $specialist['total'] ?></td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($specialists)): ?>
                            <tr>
                                <td colspan="2">Нет данных.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <br>
        <a href="/sites/andrey/pages/dashboard.php" class="btn btn_dark">Назад</a>
    </div>
</section>

<?php include '../../includes/footer.php'; ?>