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
?>

<title>Новости</title>

<div class="wrapper">
<div class="container">
    
<div class="header-with-search">
    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Введите название новости"
               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit" class="search-btn">Поиск</button>
        <?php if (!empty($_GET['search'])): ?>
            <a href="?" class="reset-btn">Сбросить</a>
        <?php endif; ?>
    </form>
</div>

    <h1><b>НОВОСТИ</b></h1>

    <form method="GET" class="sort-form">
    <label for="sort" class="sort-label">Сортировка:</label>
    <select name="sort" id="sort" onchange="this.form.submit()">
        <option value="newest" <?= ($_GET['sort'] ?? 'newest') === 'newest' ? 'selected' : '' ?>>Сначала новые</option>
        <option value="oldest" <?= ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Сначала старые</option>
    </select>
    <?php if (!empty($_GET['search'])): ?>
        <input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search']) ?>">
    <?php endif; ?>
    </form>

    <div class="news-grid">
        <?php
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'newest';
        $order = $sort === 'oldest' ? 'ASC' : 'DESC';

        if (!empty($search)) {
            $stmt = $pdo->prepare("SELECT * FROM News WHERE title_news LIKE :search ORDER BY date_published $order");
            $stmt->execute(['search' => '%' . $search . '%']);
        } else {
            $stmt = $pdo->query("SELECT * FROM News ORDER BY date_published $order");
        }
        $stadiums = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($stadiums) > 0) {
            foreach ($stadiums as $row) {
                echo "<div class='news-card'>";
                echo '<a class="news-card-btn" href="newspage.php?id=' . $row['news_id'] . '">';
                echo '<img class="image-news" src="' . htmlspecialchars($row['img_news']) . '" alt="Изображение стадиона">';
                echo "<div class='news-content'>";
                echo "<h3>" . htmlspecialchars($row['title_news']) . "</h3>";
                echo "<p>" . nl2br(htmlspecialchars(mb_strimwidth($row['content_news'], 0, 50, '...'))) . "</p>";
                echo "<small>" . htmlspecialchars($row['date_published']) . " | " . htmlspecialchars($row['author_news']) ."</small>";
                echo "</div>";
                echo '</a>';
                echo "</div>";
            }
        } else {
            echo "<p style='text-align: center; font-size: 18px; color: white;'>Стадионы не найдены.</p>";
        }
        ?>
    </div>
</div>
</div>

<?php
require_once 'blade/footer.php';
?>
