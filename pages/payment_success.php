<?php
require_once '../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['order'])) {
    header('Location: /sites/andrey/pages/subscribe.php');
    exit;
}

$order = $_SESSION['order'];

$company_name = $order['company_name'];
$tariff = $order['tariff'];
$email = $order['email'];
$password = password_hash($order['password'], PASSWORD_DEFAULT);
$contact = $order['contact'];

try {
    $checkUser = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $checkUser->execute([$email]);

    if ($checkUser->fetch()) {
        die('Пользователь с таким email уже существует. <a href="/sites/andrey/pages/subscribe.php">Вернуться назад</a>');
    }

    $pdo->beginTransaction();

    $companyStmt = $pdo->prepare("
        INSERT INTO companies (name, contact, tariff, subscription_status)
        VALUES (?, ?, ?, 'active')
    ");
    $companyStmt->execute([$company_name, $contact, $tariff]);

    $company_id = $pdo->lastInsertId();

    $userStmt = $pdo->prepare("
        INSERT INTO users (company_id, full_name, email, password, role)
        VALUES (?, ?, ?, ?, 'admin')
    ");
    $userStmt->execute([
        $company_id,
        'Администратор компании',
        $email,
        $password
    ]);

    $user_id = $pdo->lastInsertId();

    $defaultCategories = [
        'Оборудование',
        'Интернет',
        'Программное обеспечение',
        'Учетные записи',
        'Доступы',
        'Другое'
    ];

    $categoryStmt = $pdo->prepare("
        INSERT INTO categories (company_id, name)
        VALUES (?, ?)
    ");

    foreach ($defaultCategories as $category) {
        $categoryStmt->execute([$company_id, $category]);
    }

    $pdo->commit();

    $_SESSION['user_id'] = $user_id;
    $_SESSION['company_id'] = $company_id;
    $_SESSION['role'] = 'admin';
    $_SESSION['email'] = $email;

    unset($_SESSION['order']);

    header('Location: /sites/andrey/pages/dashboard.php');
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    die('Ошибка создания подписки: ' . $e->getMessage());
}