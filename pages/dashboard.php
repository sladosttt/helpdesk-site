<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /sites/andrey/pages/login.php');
    exit;
}

if ($_SESSION['role'] === 'admin') {
    header('Location: admin/dashboard.php');
    exit;
}

if ($_SESSION['role'] === 'specialist') {
    header('Location: specialist/dashboard.php');
    exit;
}

if ($_SESSION['role'] === 'employee') {
    header('Location: employee/dashboard.php');
    exit;
}

header('Location: /sites/andrey/pages/login.php');
exit;