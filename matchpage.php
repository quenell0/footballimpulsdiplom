<?php
session_start();
require_once './assets/classes/dbd.php';
$db = new Dbh();
$pdo = $db->connect_pdo();

require_once 'blade/header.php';

if (isset($_SESSION['user_login'])) {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        require_once 'blade/menuadmin.php';
    } else {
        require_once 'blade/menuuser.php';
    }
} else {
    require_once 'blade/menu.php';
}

if (!isset($_GET['id'])) {
    echo "<h2 style='color:white;text-align:center;'>Новость не найдена.</h2>";
    require_once 'blade/footer.php';
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT Matches.*, 
    COALESCE(c1.name_comand, Matches.home_team) AS home_team_name,
    COALESCE(c2.name_comand, Matches.away_team) AS away_team_name
    FROM Matches 
    LEFT JOIN Comands c1 ON Matches.home_team = c1.comand_id
    LEFT JOIN Comands c2 ON Matches.away_team = c2.comand_id
    WHERE Matches.id_match = ?");
$stmt->execute([$id]);
$news = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$news) {
    echo "<h2 style='color:white;text-align:center;'>Новость не найдена.</h2>";
    require_once 'blade/footer.php';
    exit();
}

$matchDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $news['date_match'] . ' ' . $news['time_match']);
$now = new DateTime();
$isMatchFuture = $matchDateTime && $matchDateTime > $now;
?>

<a href="javascript:history.back()" class="back-button">← Назад</a>

<div class="page-layout">

    <!-- Левая колонка -->
    <div class="related-column left">
    <?php
        // Получаем матчи для левой колонки
        $stmt_left = $pdo->prepare("SELECT * FROM Matches WHERE id_match != ? ORDER BY id_match DESC LIMIT 2");
        $stmt_left->execute([$id]);
        $left_matches = $stmt_left->fetchAll(PDO::FETCH_ASSOC);

        foreach ($left_matches as $row): ?>
            <div class="news-card">
                <a class="news-card-btn" href="matchpage.php?id=<?= $row['id_match'] ?>">
                    <img class="image-news" src="<?= htmlspecialchars($row['img_match']) ?>" alt="Матч">
                    <div class="news-content">
                        <h3><?= htmlspecialchars($row['title_match']) ?></h3>
                        <p><?= nl2br(htmlspecialchars(mb_strimwidth($row['description_match'], 0, 80, '...'))) ?></p>
                        <small>Дата: <?= htmlspecialchars($row['date_match']) ?> | <?= htmlspecialchars($row['time_match']) ?></small>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Основная статья -->
    <div class="article-container">
        <h1 class="article-title"><?php echo htmlspecialchars($news['title_match']); ?></h1>
        <img class="article-image" src="<?php echo htmlspecialchars($news['img_match']); ?>" alt="Изображение матча">
        <div class="article-meta">
            Дата: <?php echo htmlspecialchars($news['date_match']); ?> | <?php echo htmlspecialchars($news['time_match']); ?>
        </div>
        <div class="article-content">
            <?php echo nl2br(htmlspecialchars($news['description_match'])); ?>
        </div>
        <div class="article-location">
            Команды: <?php echo htmlspecialchars($news['home_team_name']); ?> vs <?php echo htmlspecialchars($news['away_team_name']); ?>
            <br>
            Счёт: <?php echo htmlspecialchars($news['score_home_team']); ?>:<?php echo htmlspecialchars($news['score_away_team']); ?>
        </div>
        <div class="ticket-action-page">
            <?php if ($isMatchFuture): ?>
                <a class="ticket-btn" href="buy_ticket.php?id=<?= $news['id_match'] ?>">Купить билет</a>
            <?php else: ?>
                <span class="ticket-badge finished">Матч завершён</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Правая колонка -->
    <!-- Правая колонка -->
    <div class="related-column right">
    <?php
        // Получаем 2 матча для правой колонки (со смещением)
        $stmt_right = $pdo->prepare("SELECT * FROM Matches WHERE id_match != ? ORDER BY id_match DESC LIMIT 2 OFFSET 2");
        $stmt_right->execute([$id]);
        $right_matches = $stmt_right->fetchAll(PDO::FETCH_ASSOC);

        foreach ($right_matches as $row): ?>
            <div class="news-card">
                <a class="news-card-btn" href="matchpage.php?id=<?= $row['id_match'] ?>">
                    <img class="image-news" src="<?= htmlspecialchars($row['img_match']) ?>" alt="Матч">
                    <div class="news-content">
                        <h3><?= htmlspecialchars($row['title_match']) ?></h3>
                        <p><?= nl2br(htmlspecialchars(mb_strimwidth($row['description_match'], 0, 80, '...'))) ?></p>
                        <small>Дата: <?= htmlspecialchars($row['date_match']) ?> | <?= htmlspecialchars($row['time_match']) ?></small>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<!-- Кнопка вверх -->
<button onclick="scrollToTop()" id="scrollTopBtn" title="Наверх">↑</button>

<script>
    // Показ кнопки при скролле
    window.onscroll = function () {
        const btn = document.getElementById("scrollTopBtn");
        if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
            btn.style.display = "block";
        } else {
            btn.style.display = "none";
        }
    };

    // Прокрутка вверх
    function scrollToTop() {
        window.scrollTo({top: 0, behavior: 'smooth'});
    }
</script>

<?php require_once 'blade/footer.php'; ?>
