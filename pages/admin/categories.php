<?php
require_once 'config/database.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pages/login.php');
    exit;
}

$company_id = $_SESSION['company_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    if (empty($name)) {
        $error = 'Введите название категории.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO categories (company_id, name)
            VALUES (?, ?)
        ");
        $stmt->execute([$company_id, $name]);

        $success = 'Категория успешно добавлена.';
    }
}

if (isset($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];

    $stmt = $pdo->prepare("
        DELETE FROM categories
        WHERE id = ? AND company_id = ?
    ");
    $stmt->execute([$delete_id, $company_id]);

    header('Location: categories.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT * FROM categories
    WHERE company_id = ?
    ORDER BY id DESC
");
$stmt->execute([$company_id]);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="section">
    <div class="container">
        <h1>Категории заявок</h1>

        <p class="form-text">
            Здесь администратор может настроить типы обращений для своей компании.
        </p>

        <?php if ($error): ?>
            <div class="alert alert_error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert_success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="grid-two">
            <div class="form-wrapper small-form">
                <h2>Добавить категорию</h2>

                <form method="POST" class="form">
                    <div class="form-group">
                        <label>Название категории</label>
                        <input 
                            type="text" 
                            name="name" 
                            placeholder="Например: Интернет"
                            required
                        >
                    </div>

                    <button type="submit" class="btn">Добавить</button>
                </form>
            </div>

            <div class="table-wrapper">
                <h2>Список категорий</h2>

                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= $category['id'] ?></td>
                                <td><?= htmlspecialchars($category['name']) ?></td>
                                <td>
                                    <a 
                                        href="categories.php?delete=<?= $category['id'] ?>" 
                                        class="delete-link"
                                        onclick="return confirm('Удалить категорию?')"
                                    >
                                        Удалить
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="3">Категории пока не добавлены.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <br>
        <a href="/pages/dashboard.php" class="btn btn_dark">Назад</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>