<?php
require_once '../../config/database.php';
include '../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header('Location: /sites/andrey/pages/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$company_id = $_SESSION['company_id'];
$error = '';

$categoriesStmt = $pdo->prepare("
    SELECT * FROM categories 
    WHERE company_id = ?
    ORDER BY name ASC
");
$categoriesStmt->execute([$company_id]);
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category_id = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;
    $priority = $_POST['priority'];
    $description = trim($_POST['description']);

    if (empty($title) || empty($description)) {
        $error = 'Заполните тему и описание заявки.';
    } elseif (!in_array($priority, ['low', 'medium', 'high'])) {
        $error = 'Некорректный приоритет.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO tickets 
            (company_id, user_id, category_id, title, description, priority, status)
            VALUES (?, ?, ?, ?, ?, ?, 'new')
        ");

        $stmt->execute([
            $company_id,
            $user_id,
            $category_id,
            $title,
            $description,
            $priority
        ]);

        header('Location: /sites/andrey/pages/dashboard.php');
        exit;
    }
}
?>

<section class="section">
    <div class="container">
        <div class="form-wrapper">
            <h1>Создание заявки</h1>

            <p class="form-text">
                Опишите проблему, чтобы специалист технической поддержки мог быстрее ее обработать.
            </p>

            <?php if ($error): ?>
                <div class="alert alert_error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" class="form">
                <div class="form-group">
                    <label>Тема заявки</label>
                    <input 
                        type="text" 
                        name="title" 
                        placeholder="Например: Не работает интернет"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Категория</label>
                    <select name="category_id">
                        <option value="">Без категории</option>

                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>">
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Приоритет</label>
                    <select name="priority" required>
                        <option value="low">Низкий</option>
                        <option value="medium" selected>Средний</option>
                        <option value="high">Высокий</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Описание проблемы</label>
                    <textarea 
                        name="description" 
                        rows="6" 
                        placeholder="Подробно опишите проблему"
                        required
                    ></textarea>
                </div>

                <button type="submit" class="btn">Отправить заявку</button>
            </form>

            <br>
            <a href="/sites/andrey/pages/dashboard.php" class="btn btn_dark">Назад</a>
        </div>
    </div>
</section>

<?php include '../../includes/footer.php'; ?>