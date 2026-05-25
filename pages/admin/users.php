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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (!in_array($role, ['admin', 'specialist', 'employee'])) {
        $error = 'Некорректная роль пользователя.';
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = 'Пользователь с таким email уже существует.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO users (company_id, full_name, email, password, role)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([$company_id, $full_name, $email, $hash, $role]);

            $success = 'Пользователь успешно добавлен.';
        }
    }
}

if (isset($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];

    if ($delete_id !== (int) $_SESSION['user_id']) {
        $stmt = $pdo->prepare("
            DELETE FROM users 
            WHERE id = ? AND company_id = ?
        ");
        $stmt->execute([$delete_id, $company_id]);

        header('Location: users.php');
        exit;
    }
}

$stmt = $pdo->prepare("
    SELECT * FROM users 
    WHERE company_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$company_id]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="section">
    <div class="container">
        <h1>Управление сотрудниками</h1>

        <p class="form-text">
            Здесь администратор компании может добавлять сотрудников, специалистов и других администраторов.
        </p>

        <?php if ($error): ?>
            <div class="alert alert_error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert_success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="grid-two">
            <div class="form-wrapper small-form">
                <h2>Добавить пользователя</h2>

                <form method="POST" class="form">
                    <div class="form-group">
                        <label>ФИО</label>
                        <input type="text" name="full_name" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label>Пароль</label>
                        <input type="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label>Роль</label>
                        <select name="role" required>
                            <option value="employee">Сотрудник</option>
                            <option value="specialist">Специалист</option>
                            <option value="admin">Администратор</option>
                        </select>
                    </div>

                    <button type="submit" class="btn">Добавить</button>
                </form>
            </div>

            <div class="table-wrapper">
                <h2>Пользователи компании</h2>

                <table class="table">
                    <thead>
                        <tr>
                            <th>ФИО</th>
                            <th>Email</th>
                            <th>Роль</th>
                            <th>Дата</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['full_name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <?php
                                    if ($user['role'] === 'admin') echo 'Администратор';
                                    if ($user['role'] === 'specialist') echo 'Специалист';
                                    if ($user['role'] === 'employee') echo 'Сотрудник';
                                    ?>
                                </td>
                                <td><?= date('d.m.Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <a 
                                            href="users.php?delete=<?= $user['id'] ?>" 
                                            class="delete-link"
                                            onclick="return confirm('Удалить пользователя?')"
                                        >
                                            Удалить
                                        </a>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>

        <br>
        <a href="/sites/andrey/pages/dashboard.php" class="btn btn_dark">Назад</a>
    </div>
</section>

<?php include '../../includes/footer.php'; ?>