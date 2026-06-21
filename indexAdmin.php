<?php
require_once 'assets/menuAdmin.php';
require_once 'assets/classes/dbd.php';
require_once 'assets/classes/News.php';
require_once 'assets/classes/Matches.php';
require_once 'assets/classes/Players.php';
require_once 'assets/classes/Teams.php';
require_once 'assets/classes/Stadium.php';

$dbh = new Dbh();
$db = $dbh->connect_pdo();
$newsModel = new News($db);
$matchesModel = new Matches($db);
$playersModel = new Player($db);
$teamsModel = new Teams($db);
$stadiumModel = new Stadium($db);
?>

<div class="admin-main-inner">
    <div class="welcome-card">
        <h1><i class="fas fa-tachometer-alt"></i> Панель управления Импульс</h1>
        <p>Добро пожаловать в административную панель футбольного клуба «Импульс». Здесь вы можете управлять всем контентом сайта: новостями, матчами, игроками, командами и стадионами.</p>
    </div>

    <!-- Карточки статистики -->
    <div class="stats-grid">
        <a href="tableNews.php" class="stat-card">
            <i class="fas fa-newspaper"></i>
            <h3><?= $newsModel->getAll()->rowCount() ?></h3>
            <p>Новостей</p>
        </a>
        <a href="tableMatches.php" class="stat-card">
            <i class="fas fa-futbol"></i>
            <h3><?= $matchesModel->getAll()->rowCount() ?></h3>
            <p>Матчей</p>
        </a>
        <a href="tablePlayers.php" class="stat-card">
            <i class="fas fa-users"></i>
            <h3><?= $playersModel->getAll()->rowCount() ?></h3>
            <p>Футболистов</p>
        </a>
        <a href="tableTeams.php" class="stat-card">
            <i class="fas fa-shield-alt"></i>
            <h3><?= $teamsModel->getAll()->rowCount() ?></h3>
            <p>Команд</p>
        </a>
        <a href="tableStadiums.php" class="stat-card">
            <i class="fas fa-home"></i>
            <h3><?= $stadiumModel->getAll()->rowCount() ?></h3>
            <p>Стадионов</p>
        </a>
    </div>

    <!-- Информационные блоки -->
    <div class="info-grid">
        <div class="info-card">
            <h3><i class="fas fa-info-circle"></i> О клубе</h3>
            <ul>
                <li><span class="label">Основание:</span><span class="value">2024 год</span></li>
                <li><span class="label">Главный тренер:</span><span class="value">Дмитрий Морозов</span></li>
                <li><span class="label">Капитан:</span><span class="value">Алексей Капитанов</span></li>
                <li><span class="label">Домашний стадион:</span><span class="value">Импульс арена</span></li>
                <li><span class="label">Вместимость:</span><span class="value">25 000</span></li>
            </ul>
        </div>

        <div class="info-card">
            <h3><i class="fas fa-chart-simple"></i> Статистика сезона</h3>
            <ul>
                <li><span class="label">Сыграно матчей:</span><span class="value">24</span></li>
                <li><span class="label">Победы:</span><span class="value">16</span></li>
                <li><span class="label">Ничьи:</span><span class="value">5</span></li>
                <li><span class="label">Поражения:</span><span class="value">3</span></li>
                <li><span class="label">Забито голов:</span><span class="value">52</span></li>
            </ul>
        </div>

        <div class="info-card">
            <h3><i class="fas fa-bolt"></i> Быстрые действия</h3>
            <div class="quick-actions">
                <a href="tableNews.php" class="quick-btn"><i class="fas fa-plus"></i> Добавить новость</a>
                <a href="tableMatches.php" class="quick-btn"><i class="fas fa-plus"></i> Добавить матч</a>
                <a href="tablePlayers.php" class="quick-btn"><i class="fas fa-plus"></i> Добавить игрока</a>
                <a href="tableTeams.php" class="quick-btn"><i class="fas fa-plus"></i> Добавить команду</a>
                <a href="tableStadiums.php" class="quick-btn"><i class="fas fa-plus"></i> Добавить стадион</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'assets/footer.php'; ?>
