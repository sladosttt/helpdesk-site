<?php include '../includes/header.php'; ?>

<section class="section">
    <div class="container">
        <h1>Тарифы HelpDesk System</h1>

        <p class="form-text">
            Выберите подходящий тариф для вашей компании и оформите подписку на систему обработки заявок.
        </p>

        <div class="tariffs-grid">
            <div class="tariff-card">
                <h2>Базовый</h2>
                <p class="tariff-price">990 ₽ / месяц</p>

                <ul>
                    <li>До 50 заявок в месяц</li>
                    <li>До 5 сотрудников</li>
                    <li>1 специалист поддержки</li>
                    <li>Базовая статистика</li>
                </ul>

                <a href="/pages/subscribe.php?tariff=basic" class="btn">Выбрать тариф</a>
            </div>

            <div class="tariff-card tariff-card_popular">
                <div class="popular-label">Популярный</div>

                <h2>Стандарт</h2>
                <p class="tariff-price">1990 ₽ / месяц</p>

                <ul>
                    <li>До 300 заявок в месяц</li>
                    <li>До 25 сотрудников</li>
                    <li>До 5 специалистов</li>
                    <li>Расширенная статистика</li>
                </ul>

                <a href="/pages/subscribe.php?tariff=standard" class="btn">Выбрать тариф</a>
            </div>

            <div class="tariff-card">
                <h2>Профессиональный</h2>
                <p class="tariff-price">3990 ₽ / месяц</p>

                <ul>
                    <li>Без ограничений по заявкам</li>
                    <li>Без ограничений по сотрудникам</li>
                    <li>Без ограничений по специалистам</li>
                    <li>Полная статистика компании</li>
                </ul>

                <a href="subscribe.php?tariff=pro" class="btn">Выбрать тариф</a>
            </div>
        </div>

        <br>
        <a href="/index.php" class="btn btn_dark">На главную</a>
    </div>
</section>

<?php include '../includes/footer.php'; ?>