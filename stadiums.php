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

<title>Стадионы</title>

<div class="wrapper">
<div class="container">
    
<div class="header-with-search">
    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Введите название стадиона"
               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit" class="search-btn">Поиск</button>
        <?php if (!empty($_GET['search'])): ?>
            <a href="?" class="reset-btn">Сбросить</a>
        <?php endif; ?>
    </form>
</div>

    <h1><b>СТАДИОНЫ</b></h1>
    <div class="news-grid">
        <?php
        $search = $_GET['search'] ?? '';
        if (!empty($search)) {
            $stmt = $pdo->prepare("SELECT * FROM Stadium WHERE name_stadium LIKE :search ORDER BY stadium_id DESC");
            $stmt->execute(['search' => '%' . $search . '%']);
        } else {
            $stmt = $pdo->query("SELECT * FROM Stadium ORDER BY stadium_id DESC");
        }

        $stadiums = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($stadiums) > 0) {
            foreach ($stadiums as $row) {
                echo "<div class='news-card'>";
                echo '<a class="news-card-btn" href="stadiumpage.php?id=' . $row['stadium_id'] . '">';
                echo '<img class="image-news" src="' . htmlspecialchars($row['img_stadium']) . '" alt="Изображение стадиона">';
                echo "<div class='news-content'>";
                echo "<h3>" . htmlspecialchars($row['name_stadium']) . "</h3>";
                echo "<p>" . htmlspecialchars($row['location_stadium']) . "</p>";
                echo "<small>Вместимость: " . htmlspecialchars($row['capacity_stadium']) . " чел.</small>";
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
