<?php
require_once '../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("
        SELECT users.*, companies.subscription_status 
        FROM users
        LEFT JOIN companies ON users.company_id = companies.id
        WHERE users.email = ?
        LIMIT 1
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        if ($user['subscription_status'] !== 'active') {
            $error = 'Подписка компании неактивна.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['company_id'] = $user['company_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];

            header('Location: /sites/andrey/pages/dashboard.php');
            exit;
        }
    } else {
        $error = 'Неверный email или пароль.';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход | HelpDesk System</title>
    <link rel="stylesheet" href="/sites/andrey/assets/css/style.css">
</head>
<body class="login-body">

<div class="login-bg">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="grid-light"></div>

    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>
    <div class="floating-shape shape-3"></div>
</div>

<section class="login-screen">
    <div class="login-card">
        <div class="login-logo">HelpDesk</div>

        <h1>Вход в систему</h1>

        <p>
            Авторизуйтесь для управления заявками, сотрудниками и технической поддержкой компании.
        </p>

        <?php if (!empty($error)): ?>
            <div class="alert alert_error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="login-form">
            <div class="login-input">
                <input type="email" name="email" placeholder="Email администратора" required>
            </div>

            <div class="login-input">
                <input type="password" name="password" placeholder="Пароль" required>
            </div>

            <button type="submit" class="login-btn">Войти</button>
        </form>

        <div class="login-links">
            <a href="/sites/andrey/index.php">На главную</a>
            <a href="/sites/andrey/pages/subscribe.php">Оформить подписку</a>
        </div>
    </div>
</section>

</body>
</html>