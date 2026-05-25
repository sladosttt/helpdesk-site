<?php
require_once '../../config/database.php';
include '../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /sites/andrey/pages/login.php');
    exit;
}

$company_id = $_SESSION['company_id'];
$error = '';
$success = '';

$stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
$stmt->execute([$company_id]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    die('Компания не найдена.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $contact = trim($_POST['contact']);

    if (empty($name)) {
        $error = 'Название компании не может быть пустым.';
    } else {
        $update = $pdo->prepare("
            UPDATE companies
            SET name = ?, contact = ?
            WHERE id = ?
        ");
        $update->execute([$name, $contact, $company_id]);

        $success = 'Данные компании успешно обновлены.';

        $stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
        $stmt->execute([$company_id]);
        $company = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<section class="section">
    <div class="container">
        <h1>Настройки компании</h1>

        <p class="form-text">
            Здесь можно изменить основные данные компании и посмотреть информацию о подписке.
        </p>

        <?php if ($error): ?>
            <div class="alert alert_error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert_success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="grid-two">
            <div class="form-wrapper small-form">
                <h2>Данные компании</h2>

                <form method="POST" class="form">
                    <div class="form-group">
                        <label>Название компании</label>
                        <input 
                            type="text" 
                            name="name" 
                            value="<?= htmlspecialchars($company['name']) ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Контакт для связи</label>
                        <input 
                            type="text" 
                            name="contact" 
                            value="<?= htmlspecialchars($company['contact']) ?>"
                            placeholder="Телефон, Telegram или email"
                        >
                    </div>

                    <button type="submit" class="btn">Сохранить</button>
                </form>
            </div>

            <div class="table-wrapper">
                <h2>Информация о подписке</h2>

                <div class="payment-info">
                    <p><strong>Тариф:</strong> <?= htmlspecialchars($company['tariff']) ?></p>
                    <p><strong>Статус:</strong> <?= htmlspecialchars($company['subscription_status']) ?></p>
                    <p><strong>Дата регистрации:</strong> <?= date('d.m.Y H:i', strtotime($company['created_at'])) ?></p>
                </div>
            </div>
        </div>

        <br>
        <a href="/sites/andrey/pages/dashboard.php" class="btn btn_dark">Назад</a>
    </div>
</section>

<?php include '../../includes/footer.php'; ?>