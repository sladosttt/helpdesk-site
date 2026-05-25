<?php
require_once '../../config/database.php';
include '../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /sites/andrey/pages/login.php');
    exit;
}

$company_id = $_SESSION['company_id'];

$companyStmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
$companyStmt->execute([$company_id]);
$company = $companyStmt->fetch(PDO::FETCH_ASSOC);

$usersCount = $pdo->prepare("SELECT COUNT(*) FROM users WHERE company_id = ?");
$usersCount->execute([$company_id]);
$users_total = $usersCount->fetchColumn();

$ticketsCount = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE company_id = ?");
$ticketsCount->execute([$company_id]);
$tickets_total = $ticketsCount->fetchColumn();

$newTicketsCount = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE company_id = ? AND status = 'new'");
$newTicketsCount->execute([$company_id]);
$new_tickets = $newTicketsCount->fetchColumn();
?>

<section class="section">
    <div class="container">
        <h1>Панель администратора</h1>

        <div class="dashboard-header">
            <div>
                <h2><?= htmlspecialchars($company['name']) ?></h2>
                <p>Тариф: <?= htmlspecialchars($company['tariff']) ?></p>
                <p>Статус подписки: <?= htmlspecialchars($company['subscription_status']) ?></p>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <span><?= $users_total ?></span>
                <p>Пользователей</p>
            </div>

            <div class="stat-card">
                <span><?= $tickets_total ?></span>
                <p>Всего заявок</p>
            </div>

            <div class="stat-card">
                <span><?= $new_tickets ?></span>
                <p>Новых заявок</p>
            </div>
        </div>

        <div class="actions-grid">
            <a href="users.php" class="action-card">Управление сотрудниками</a>
            <a href="tickets.php" class="action-card">Все заявки компании</a>
            <a href="categories.php" class="action-card">Категории заявок</a>
            <a href="statistics.php" class="action-card">Статистика</a>
            <a href="settings.php" class="action-card">Настройки компании</a>
        </div>
    </div>
</section>

<?php include '../../includes/footer.php'; ?>