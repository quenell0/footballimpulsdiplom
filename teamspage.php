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
    $stmt = $pdo->prepare("SELECT Comands.*, Players.name_player AS trener_name 
                            FROM Comands 
                            LEFT JOIN Players ON Comands.trener_comand = Players.player_id
                            WHERE Comands.comand_id = ?");
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

<div class="page-layout">

    <div class="article-container">
        <h1 class="article-title"><?php echo htmlspecialchars($news['name_comand']); ?></h1>
        <div class="image-section image-left">
        <img class="article-image" src="<?php echo htmlspecialchars($news['icon_comand']); ?>" alt="Изображение стадиона">
        <div class="article-content">
            <?php echo nl2br(htmlspecialchars($news['discription_comand'])); ?>
        </div>
        </div>
        <div class="article-location">
            Тренер: <?php echo htmlspecialchars($news['trener_name']); ?>
        </div>
    </div>

    <div class="related-column right">
    <?php
        // Получаем 3 новости для левой колонки
        $stmt_left = $pdo->prepare("SELECT * FROM Comands WHERE comand_id != ? ORDER BY comand_id DESC LIMIT 2");
        $stmt_left->execute([$id]);
        $left_news = $stmt_left->fetchAll(PDO::FETCH_ASSOC);

        foreach ($left_news as $row): ?>
            <div class="news-card">
                <a class="news-card-btn" href="teamspage.php?id=<?= $row['comand_id'] ?>">
                    <img class="image-news" src="<?= htmlspecialchars($row['img_comand']) ?>" alt="Стадион">
                    <div class="news-content">
                        <h3><?= htmlspecialchars($row['name_comand']) ?></h3>
                        <p><?= nl2br(htmlspecialchars(mb_strimwidth($row['discription_comand'], 0, 100, '...'))) ?></p>
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
