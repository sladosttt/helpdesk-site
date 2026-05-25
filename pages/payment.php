<?php
include '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /sites/andrey/pages/subscribe.php');
    exit;
}

$company_name = trim($_POST['company_name']);
$tariff = trim($_POST['tariff']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$password_confirm = $_POST['password_confirm'];
$contact = trim($_POST['contact']);

$tariff_prices = [
    'basic' => 990,
    'standard' => 1990,
    'pro' => 3990
];

$tariff_names = [
    'basic' => 'Базовый',
    'standard' => 'Стандарт',
    'pro' => 'Профессиональный'
];

if ($password !== $password_confirm) {
    die('Пароли не совпадают. <a href="/sites/andrey/pages/subscribe.php">Вернуться назад</a>');
}

if (!isset($tariff_prices[$tariff])) {
    die('Некорректный тариф. <a href="/sites/andrey/pages/subscribe.php">Вернуться назад</a>');
}

$_SESSION['order'] = [
    'company_name' => $company_name,
    'tariff' => $tariff,
    'email' => $email,
    'password' => $password,
    'contact' => $contact
];
?>

<section class="section">
    <div class="container">
        <div class="form-wrapper">
            <h1>Тестовая оплата</h1>

            <p class="form-text">
                Это демонстрационная оплата.
            </p>

            <div class="payment-info">
                <p><strong>Компания:</strong> <?= htmlspecialchars($company_name) ?></p>
                <p><strong>Тариф:</strong> <?= $tariff_names[$tariff] ?></p>
                <p><strong>Сумма:</strong> <?= $tariff_prices[$tariff] ?> ₽ / месяц</p>
            </div>

            <form action="/sites/andrey/pages/payment_success.php" method="POST" class="form">
                <div class="form-group">
                    <label>Номер карты</label>
                    <input 
                        type="text" 
                        name="card_number" 
                        placeholder="0000 0000 0000 0000" 
                        maxlength="19"
                        required
                    >
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Срок действия</label>
                        <input 
                            type="text" 
                            name="card_date" 
                            placeholder="MM/YY" 
                            maxlength="5"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>CVV</label>
                        <input 
                            type="password" 
                            name="cvv" 
                            placeholder="***" 
                            maxlength="3"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label>Имя владельца карты</label>
                    <input 
                        type="text" 
                        name="card_name" 
                        placeholder="IVAN IVANOV"
                        required
                    >
                </div>

                <button type="submit" class="btn">Оплатить</button>
            </form>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>