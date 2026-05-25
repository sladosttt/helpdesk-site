<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$tariff = $_GET['tariff'] ?? '';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оформление подписки | HelpDesk System</title>
    <link rel="stylesheet" href="/assets/css/style.css">
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
    <div class="login-card subscribe-card">
        <div class="login-logo">HelpDesk System</div>

        <h1>Оформление подписки</h1>

        <p>
            Зарегистрируйте компанию, выберите тариф и получите доступ к системе обработки заявок.
        </p>

        <form action="/pages/payment.php" method="POST" class="login-form">
            <div class="login-input">
                <input 
                    type="text" 
                    name="company_name" 
                    placeholder="Название компании. Например, ООО «Ромашка»" 
                    required
                >
            </div>

            <div class="login-input">
                <select name="tariff" required>
                    <option value="">Выберите тариф</option>
                    <option value="basic" <?= $tariff === 'basic' ? 'selected' : '' ?>>
                        Базовый — 990 ₽ / месяц
                    </option>
                    <option value="standard" <?= $tariff === 'standard' ? 'selected' : '' ?>>
                        Стандарт — 1990 ₽ / месяц
                    </option>
                    <option value="pro" <?= $tariff === 'pro' ? 'selected' : '' ?>>
                        Профессиональный — 3990 ₽ / месяц
                    </option>
                </select>
            </div>

            <div class="login-input">
                <input 
                    type="email" 
                    name="email" 
                    placeholder="Email администратора" 
                    required
                >
            </div>

            <div class="login-input">
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Пароль" 
                    required
                >
            </div>

            <div class="login-input">
                <input 
                    type="password" 
                    name="password_confirm" 
                    placeholder="Подтверждение пароля" 
                    required
                >
            </div>

            <div class="login-input">
                <input 
                    type="text" 
                    name="contact" 
                    placeholder="Дополнительный контакт для связи"
                >
            </div>

            <button type="submit" class="login-btn">Перейти к оплате</button>
        </form>

        <div class="login-links">
            <a href="/index.php">На главную</a>
            <a href="/pages/login.php">Уже есть аккаунт?</a>
        </div>
    </div>
</section>

</body>
</html>