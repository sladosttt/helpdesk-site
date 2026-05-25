<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base = '/sites/andrey';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>HelpDesk System</title>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="container header__inner">
        <a href="<?= $base ?>/index.php" class="logo">HelpDesk System</a>

        <nav class="nav">
            <a href="<?= $base ?>/index.php">Главная</a>
            <a href="<?= $base ?>/pages/tariffs.php">Тарифы</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= $base ?>/pages/dashboard.php">Кабинет</a>
                <a href="<?= $base ?>/pages/logout.php">Выйти</a>
            <?php else: ?>
                <a href="<?= $base ?>/pages/login.php">Войти</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main>
