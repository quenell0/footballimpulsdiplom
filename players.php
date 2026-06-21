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

<title>Футболисты</title>

<div class="wrapper players-page">
<div class="container">
    
<div class="header-with-search">
    <form method="GET" class="search-form">
        <input type="hidden" name="position" value="<?= htmlspecialchars($_GET['position'] ?? '') ?>">
        <input type="hidden" name="team" value="<?= htmlspecialchars($_GET['team'] ?? '') ?>">
        <input type="text" name="search" placeholder="Введите футболиста"
               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit" class="search-btn">Поиск</button>
        <?php if (!empty($_GET['search']) || !empty($_GET['position']) || !empty($_GET['team'])): ?>
            <a href="?" class="reset-btn">Сбросить</a>
        <?php endif; ?>
    </form>
</div>

    <h1><b>ФУТБОЛИСТЫ ФК«Импульс»</b></h1>

    <?php
        $positions = $pdo->query("SELECT * FROM Position")->fetchAll(PDO::FETCH_ASSOC);
        $teams = $pdo->query("SELECT comand_id, name_comand FROM Comands ORDER BY name_comand ASC")->fetchAll(PDO::FETCH_ASSOC);
        $selected_position = $_GET['position'] ?? '';
        $selected_team = $_GET['team'] ?? '';
    ?>
    <form method="GET" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
        <input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

        <select name="position" onchange="this.form.submit()" style="padding: 10px; border-radius: 5px; background-color: rgb(67, 67, 67); color: white; border: 1px solid rgb(88, 88, 88);">
            <option value="">Все позиции</option>
            <?php foreach ($positions as $pos): ?>
                <option value="<?= $pos['position_id'] ?>" <?= ($selected_position == $pos['position_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($pos['name_position']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="team" onchange="this.form.submit()" style="padding: 10px; border-radius: 5px; background-color: rgb(67, 67, 67); color: white; border: 1px solid rgb(88, 88, 88);">
            <option value="">Все команды</option>
            <?php foreach ($teams as $team): ?>
                <option value="<?= $team['comand_id'] ?>" <?= ($selected_team == $team['comand_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($team['name_comand']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <div class="news-grid">
        <?php
        $search = $_GET['search'] ?? '';
        $sql = "SELECT Players.*, Position.name_position AS position_name, Comands.name_comand AS team_name
        FROM Players
        LEFT JOIN Position ON Players.position = Position.position_id
        LEFT JOIN Comands ON Players.comand_id = Comands.comand_id
        WHERE 1";

$params = [];

if (!empty($search)) {
    $sql .= " AND Players.name_player LIKE :search";
    $params['search'] = '%' . $search . '%';
}

if (!empty($selected_position)) {
    $sql .= " AND Players.position = :position";
    $params['position'] = $selected_position;
}

if (!empty($selected_team)) {
    $sql .= " AND Players.comand_id = :team";
    $params['team'] = $selected_team;
}

$sql .= " ORDER BY Players.player_id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
      

        $stadiums = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($stadiums) > 0) {
            foreach ($stadiums as $row) {
                echo "<div class='news-card'>";
                echo '<a class="news-card-btn" href="playerspage.php?id=' . $row['player_id'] . '">';
                echo '<img class="image-news" src="' . htmlspecialchars($row['img_player']) . '" alt="Изображение футболиста">';
                echo "<div class='news-content'>";
                echo "<h3>" . htmlspecialchars($row['name_player']) . "</h3>";
                echo "<small>Позиция: " . htmlspecialchars($row['position_name'] ?? '—') . "</small><br>";
                if (!empty($row['team_name'])) {
                    echo "<small>Команда: " . htmlspecialchars($row['team_name']) . "</small>";
                }
                echo "</div>";
                echo '</a>';
                echo "</div>";
            }
        } else {
            echo "<p style='text-align: center; font-size: 18px; color: white;'>Футболисты не найдены.</p>";
        }
        ?>
    </div>
</div>
</div>

<?php
require_once 'blade/footer.php';
?>
