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

<title>Команды</title>

<div class="wrapper">
    <div class="container">
        <h1><b>КОМАНДЫ ФК«Импульс»</b></h1>
        <div class="news-grid">
            <?php
            $search = $_GET['search'] ?? '';
            $stmt = $pdo->query("SELECT Comands.*, Players.name_player AS trener_name FROM Comands
                                 LEFT JOIN Players ON Comands.trener_comand = Players.player_id
                                 ORDER BY Comands.comand_id DESC");

            $stadiums = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($stadiums) > 0) {
                foreach ($stadiums as $row) {
                    echo "<div class='news-card'>";
                    echo '<a class="news-card-btn" href="teamspage.php?id=' . $row['comand_id'] . '">';
                    echo '<img class="image-news" src="' . htmlspecialchars($row['img_comand']) . '" alt="Изображение стадиона">';
                    echo "<div class='news-content'>";
                    echo "<h3>" . htmlspecialchars($row['name_comand']) . "</h3>";
                    echo "<p>" . nl2br(htmlspecialchars(mb_strimwidth($row['discription_comand'], 0, 50, '...'))) . "</p>";
                    echo "<small>Тренер: " . htmlspecialchars($row['trener_name']) . "</small>";
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
