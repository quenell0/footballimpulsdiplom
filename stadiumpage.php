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
$stmt = $pdo->prepare("SELECT * FROM Stadium WHERE stadium_id = ?");
$stmt->execute([$id]);
$news = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$news) {
    echo "<h2 style='color:white;text-align:center;'>Новость не найдена.</h2>";
    require_once 'blade/footer.php';
    exit();
}
?>

<a href="javascript:history.back()" class="back-button">← Назад</a>

<div class="page-layout">

    <div class="article-container">
        <h1 class="article-title"><?php echo htmlspecialchars($news['name_stadium']); ?></h1>
        <img class="article-image" src="<?php echo htmlspecialchars($news['img_stadium']); ?>" alt="Изображение стадиона">
        <div class="article-meta">
            Вместимость: <?php echo htmlspecialchars($news['capacity_stadium']); ?> чел.
        </div>
        <div class="article-content">
            <?php echo nl2br(htmlspecialchars($news['description_stadium'])); ?>
        </div>
        <div class="article-location">
            Адрес: <?php echo htmlspecialchars($news['location_stadium']); ?>
        </div>
    </div>

    <div class="related-column right">
    <?php
        // Получаем 3 новости для левой колонки
        $stmt_left = $pdo->prepare("SELECT * FROM Stadium WHERE stadium_id != ? ORDER BY stadium_id DESC LIMIT 3");
        $stmt_left->execute([$id]);
        $left_news = $stmt_left->fetchAll(PDO::FETCH_ASSOC);

        foreach ($left_news as $row): ?>
            <div class="news-card">
                <a class="news-card-btn" href="stadiumpage.php?id=<?= $row['stadium_id'] ?>">
                    <img class="image-news" src="<?= htmlspecialchars($row['img_stadium']) ?>" alt="Стадион">
                    <div class="news-content">
                        <h3><?= htmlspecialchars($row['name_stadium']) ?></h3>
                        <p><?= nl2br(htmlspecialchars(mb_strimwidth($row['location_stadium'], 0, 80, '...'))) ?></p>
                        <small>Вместимость: <?= htmlspecialchars($row['capacity_stadium']) ?> чел.</small>
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
