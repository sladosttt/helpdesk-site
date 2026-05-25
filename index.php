<?php
require_once 'config/database.php';
include 'includes/header.php';

$stmt = $pdo->prepare("
    SELECT name 
    FROM companies
    WHERE subscription_status = 'active'
    ORDER BY created_at DESC
    LIMIT 12
");
$stmt->execute();
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="hero">
    <div class="container hero__inner">
        <div class="hero__content">
            <h1>HelpDesk-система для компаний</h1>

            <p>
                Готовое решение для приема, обработки и отслеживания заявок
                в службе технической поддержки компании.
            </p>

            <div class="hero__buttons">
                <a href="/pages/subscribe.php" class="btn">
                    Оформить подписку
                </a>

                <a href="/pages/login.php" class="btn btn_dark">
                    Войти в систему
                </a>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2>Преимущества системы</h2>

        <div class="cards">
            <div class="card">
                <h3>Быстрое внедрение</h3>
                <p>
                    Компания получает готовую систему заявок сразу после оформления подписки.
                </p>
            </div>

            <div class="card">
                <h3>Разделение ролей</h3>
                <p>
                    Администратор, специалист и сотрудник работают в отдельных кабинетах.
                </p>
            </div>

            <div class="card">
                <h3>Контроль заявок</h3>
                <p>
                    Каждая заявка имеет статус, приоритет и историю обработки.
                </p>
            </div>

            <div class="card">
                <h3>Статистика</h3>
                <p>
                    Руководитель может видеть количество и состояние обращений.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="section section_dark">
    <div class="container">
        <h2>Как работает система</h2>

        <div class="steps">
            <div class="step">1. Компания выбирает тариф</div>
            <div class="step">2. Оформляет подписку</div>
            <div class="step">3. Проходит тестовую оплату</div>
            <div class="step">4. Получает доступ в систему</div>
            <div class="step">5. Добавляет сотрудников и работает с заявками</div>
        </div>
    </div>
</section>

<section class="section clients-section">
    <div class="container">
        <h2>Наши клиенты</h2>

        <p class="clients-text">
            Компании, которые уже используют HelpDesk System в своей работе.
        </p>

        <div class="clients-grid">

            <?php if (!empty($clients)): ?>
                <?php foreach ($clients as $client): ?>
                    <div class="client-item">
                        <?= htmlspecialchars($client['name']) ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="client-item">
                    Станьте первым клиентом
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>