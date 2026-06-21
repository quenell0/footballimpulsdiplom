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

$id = $_GET['id'] ?? null; // Для безопасности, проверка на наличие ID

if ($id) {
    $stmt = $pdo->prepare("SELECT Players.*, Position.name_position AS position_name, Comands.name_comand AS team_name
                            FROM Players
                            LEFT JOIN Position ON Players.position = Position.position_id
                            LEFT JOIN Comands ON Players.comand_id = Comands.comand_id
                            WHERE Players.player_id = ?");
    $stmt->execute([$id]);
    $news = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$news) {
        echo "<h2 style='color:white;text-align:center;'>Команда не найдена.</h2>";
        require_once 'blade/footer.php';
        exit();
    }
} else {
    echo "<h2 style='color:white;text-align:center;'>Не указан ID команды.</h2>";
    require_once 'blade/footer.php';
    exit();
}

?>

<a href="javascript:history.back()" class="back-button">← Назад</a>

<div class="page-layout players-page">

    <div class="article-container">
        <div class="image-section image-left">
        <img class="article-image" src="<?php echo htmlspecialchars($news['img_player']); ?>" alt="Изображение стадиона">
        <div class="article-content">
            <h1 class="article-title"><?php echo htmlspecialchars($news['name_player']); ?></h1>
            <br>
            Позиция: <?php echo nl2br(htmlspecialchars($news['position_name'] ?? '—')); ?>
            <br>
            <?php if (!empty($news['team_name'])): ?>
            Команда: <?php echo nl2br(htmlspecialchars($news['team_name'])); ?>
            <br>
            <?php endif; ?>
            Возраст: <?php echo nl2br(htmlspecialchars($news['age_player'])); ?>
            <br>
            Национальность: <?php echo nl2br(htmlspecialchars($news['nationality_player'])); ?>
            <br>
            
        </div>
        <?php echo nl2br(htmlspecialchars($news['description_player'])); ?>
        </div>
    </div>

    <div class="related-column right">
    <?php
        // Получаем 2 связанных футболиста для блока справа
        $stmt_left = $pdo->prepare('SELECT Players.*, Position.name_position AS position_name, Comands.name_comand AS team_name
            FROM Players
            LEFT JOIN Position ON Players.position = Position.position_id
            LEFT JOIN Comands ON Players.comand_id = Comands.comand_id
            WHERE Players.player_id != ? ORDER BY Players.player_id DESC LIMIT 2');
    
        $stmt_left->execute([$id]);
        $left_news = $stmt_left->fetchAll(PDO::FETCH_ASSOC);

        foreach ($left_news as $row): ?>
            <div class="news-card">
                <a class="news-card-btn" href="playerspage.php?id=<?= $row['player_id'] ?>">
                    <img class="image-news" src="<?= htmlspecialchars($row['img_player']) ?>" alt="Стадион">
                    <div class="news-content">
                        <h3><?= htmlspecialchars($row['name_player']) ?></h3>
                        <small>Позиция: <?= htmlspecialchars($row['position_name'] ?? '—') ?></small>
                        <?php if (!empty($row['team_name'])): ?>
                            <br><small>Команда: <?= htmlspecialchars($row['team_name']) ?></small>
                        <?php endif; ?>
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
