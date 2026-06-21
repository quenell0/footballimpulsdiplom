<?php
require_once 'assets/menuAdmin.php';
require_once './assets/classes/dbd.php';
require_once './assets/classes/Matches.php';

$dbh = new Dbh();
$db = $dbh->connect_pdo();

$player = new Matches($db);
$commands = $db->query("SELECT comand_id, name_comand FROM Comands ORDER BY name_comand ASC")->fetchAll(PDO::FETCH_ASSOC);

$showAddForm = isset($_POST['addPlayer']);
$showEditForm = isset($_POST['submitEdit']);
$playerData = null;
$alertHtml = '';

if (isset($_POST['submitEdit'])) {
    $playerData = $player->readName($_POST['submitEdit']);
}

if (isset($_POST['addPlayer'])) {
    $player->title_match = $_POST['title_match'];
    $player->description_match = $_POST['description_match'];
    $player->date_match = $_POST['date_match'];
    $player->time_match = $_POST['time_match'];
    $player->home_team = $_POST['home_team'];
    $player->away_team = $_POST['away_team'];
    $player->score_home_team = (int) $_POST['score_home_team'];
    $player->score_away_team = (int) $_POST['score_away_team'];
    $player->price_ticket = (int) ($_POST['price_ticket'] ?? 0);

    $img = $_FILES['img_match']['name'];
    if ($img) {
        $target_dir = "assets/img/";
        $target_file = $target_dir . basename($img);
        move_uploaded_file($_FILES['img_match']['tmp_name'], $target_file);
        $player->img_match = $target_file;
    } else {
        $player->img_match = 'default.jpg';
    }

    if ($player->proverka($player->title_match) != 0) {
        $alertHtml = '<div class="alert alert-danger">Такой матч уже есть!</div>';
    } elseif ($player->create()) {
        echo '<div class="alert alert-success">Матч добавлен!</div>';
        echo "<meta http-equiv='refresh' content='5'>";
        $showAddForm = false;
    } else {
        $alertHtml = '<div class="alert alert-warning">Ошибка при добавлении.</div>';
    }
}

if (isset($_POST['submitUpdate'])) {
    $player->title_match = $_POST['title_match'];
    $player->description_match = $_POST['description_match'];
    $player->date_match = $_POST['date_match'];
    $player->time_match = $_POST['time_match'];
    $player->home_team = $_POST['home_team'];
    $player->away_team = $_POST['away_team'];
    $player->score_home_team = (int) $_POST['score_home_team'];
    $player->score_away_team = (int) $_POST['score_away_team'];
        $player->price_ticket = (int) ($_POST['price_ticket'] ?? 0);

    $img = $_FILES['img_match']['name'];
    if ($img) {
        $target_dir = "assets/img/";
        $target_file = $target_dir . basename($img);
        move_uploaded_file($_FILES['img_match']['tmp_name'], $target_file);
        $player->img_match = $target_file;
    } else {
        $player->img_match = $_POST['old_img_player'];
    }

    $ID = $_POST['Hid'];
    if ($player->edit($ID)) {
        echo '<div class="alert alert-success">Данные обновлены!</div>';
        echo "<meta http-equiv='refresh' content='5'>";
    } else {
        $alertHtml = '<div class="alert alert-warning">Ошибка при редактировании.</div>';
        $showEditForm = true;
        $playerData = $player->readName($ID);
    }
}

if (isset($_POST['submitDelete']) && !empty($_POST['submitDelete'])) {
    $id_match = $_POST['submitDelete'];

    if ($player->delete($id_match)) {
        echo '<div class="alert alert-success">Матч удален!</div>';
        echo "<meta http-equiv='refresh' content='2'>";
    } else {
        $alertHtml = '<div class="alert alert-danger">Ошибка при удалении матча.</div>';
    }
}
?>

<style>
.admin-table-wrap {
    margin: 30px;
    max-width: 100%;
    min-width: 0;
}

.country-table-full {
    background: rgba(30,30,40,0.95);
    border-radius: 20px;
    padding: 25px;
    border: 1px solid rgba(56,189,248,0.2);
    max-width: 100%;
    box-sizing: border-box;
}

.table-scroll-wrap {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.country-table-full h3 {
    color: #38bdf8;
    font-size: 1.5rem;
    margin-bottom: 15px;
}

.country-table-full hr {
    border-color: rgba(56,189,248,0.3);
    margin-bottom: 20px;
}

.add-toggle-button {
    display: block;
    margin: 0 auto 16px;
    padding: 12px 24px;
    font-size: 16px;
    max-width: 280px;
    width: 100%;
    text-align: center;
}

.add-form-section {
    display: none;
    margin-bottom: 24px;
    padding: 20px;
    background: rgba(20,20,28,0.5);
    border-radius: 12px;
    border: 1px solid rgba(56,189,248,0.15);
}

.add-form-section.active {
    display: block;
}

.edit-form-section {
    display: none;
    margin-bottom: 24px;
    padding: 20px;
    background: rgba(20,20,28,0.5);
    border-radius: 12px;
    border: 1px solid rgba(56,189,248,0.15);
}

.edit-form-section.active {
    display: block;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: rgba(56,189,248,0.1);
    padding: 12px;
    color: #7dd3fc;
    font-weight: 600;
}

td {
    padding: 12px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    color: #ddd;
}

tr:hover td {
    background: rgba(56,189,248,0.05);
}

.btn {
    padding: 6px 12px;
    border-radius: 20px;
    border: none;
    cursor: pointer;
    font-weight: 500;
    transition: 0.2s;
}

.btn-success {
    background: rgba(56,189,248,0.2);
    color: #7dd3fc;
}

.btn-success:hover {
    background: #38bdf8;
    color: #1a1a1a;
}

.btn-danger {
    background: rgba(220,50,50,0.2);
    color: #ff8888;
}

.btn-danger:hover {
    background: rgb(220,50,50);
    color: white;
}

.form-control {
    background: rgba(20,20,28,0.9);
    border: 1px solid rgba(56,189,248,0.3);
    border-radius: 12px;
    padding: 10px 15px;
    color: white;
    width: 100%;
    margin-bottom: 15px;
}

.form-control:focus {
    outline: none;
    border-color: #38bdf8;
    box-shadow: 0 0 5px rgba(56,189,248,0.5);
}

.alert {
    padding: 12px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
}

.alert-success {
    background: rgba(0,200,0,0.2);
    border-left: 4px solid #00cc00;
    color: #aaffaa;
}

.alert-danger {
    background: rgba(200,0,0,0.2);
    border-left: 4px solid #ff4444;
    color: #ffaaaa;
}

@media (max-width: 1000px) {
    .admin-table-wrap { margin: 15px; }
}
</style>

<div class="admin-table-wrap">
<div class="country-table-full">
    <button type="button" class="btn btn-success add-toggle-button" id="addToggleButton">
        <?= $showAddForm ? 'Скрыть форму добавления' : 'Добавить матч' ?>
    </button>

    <div id="addFormSection" class="add-form-section<?= $showAddForm ? ' active' : '' ?>">
        <h3>ДОБАВЛЕНИЕ МАТЧА</h3>
        <hr>
        <?php if (!empty($alertHtml) && isset($_POST['addPlayer'])) echo $alertHtml; ?>
        <form action="" method="post" class="fs-4" enctype="multipart/form-data">
            <p><input type="text" class="form-control fs-5" name="title_match" placeholder="Название матча" value="<?= htmlspecialchars($_POST['title_match'] ?? '') ?>" required></p>
            <p><textarea class="form-control fs-5" name="description_match" placeholder="Описание матча" rows="4" required><?= htmlspecialchars($_POST['description_match'] ?? '') ?></textarea></p>
            <p><input type="date" class="form-control fs-5" name="date_match" value="<?= htmlspecialchars($_POST['date_match'] ?? '') ?>" required></p>
            <p><input type="time" class="form-control fs-5" name="time_match" value="<?= htmlspecialchars($_POST['time_match'] ?? '') ?>" required></p>
            <p>
                <select name="home_team" class="form-control fs-5" required>
                    <option value="">Выберите хозяев</option>
                    <?php foreach ($commands as $command): ?>
                        <option value="<?= $command['comand_id'] ?>" <?= (($_POST['home_team'] ?? '') == $command['comand_id']) ? 'selected' : '' ?>><?= htmlspecialchars($command['name_comand']) ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p><input type="text" class="form-control fs-5" name="away_team" placeholder="Гости (команда)" value="<?= htmlspecialchars($_POST['away_team'] ?? '') ?>" required></p>
            <p><input type="number" class="form-control fs-5" name="score_home_team" placeholder="Счёт хозяев" value="<?= htmlspecialchars($_POST['score_home_team'] ?? '0') ?>" required></p>
            <p><input type="number" class="form-control fs-5" name="score_away_team" placeholder="Счёт гостей" value="<?= htmlspecialchars($_POST['score_away_team'] ?? '0') ?>" required></p>
            <p><input type="file" class="form-control fs-5" name="img_match" accept="image/*"></p>
            <p><input type="number" class="form-control fs-5" name="price_ticket" placeholder="Цена билета" value="<?= htmlspecialchars($_POST['price_ticket'] ?? '0') ?>" required></p>
            <p><button type="submit" name="addPlayer" class="form-control btn btn-success fs-5">&#128190; Добавить матч</button></p>
        </form>
    </div>

    <?php if ($showEditForm && $playerData): ?>
    <div class="edit-form-section active">
        <h3>РЕДАКТИРОВАНИЕ МАТЧА</h3>
        <hr>
        <?php if (!empty($alertHtml) && isset($_POST['submitUpdate'])) echo $alertHtml; ?>
        <form action="" method="post" class="fs-4" enctype="multipart/form-data">
            <p><input type="text" class="form-control fs-5" name="title_match" value="<?= htmlspecialchars($playerData['title_match']) ?>" required></p>
            <p><input type="text" class="form-control fs-5" name="description_match" value="<?= htmlspecialchars($playerData['description_match']) ?>" required></p>
            <p><input type="text" class="form-control fs-5" name="date_match" value="<?= htmlspecialchars($playerData['date_match']) ?>" required></p>
            <p><input type="text" class="form-control fs-5" name="time_match" value="<?= htmlspecialchars($playerData['time_match']) ?>" required></p>
            <p>
                <select name="home_team" class="form-control fs-5" required>
                    <option value="">Выберите хозяев</option>
                    <?php foreach ($commands as $command): ?>
                        <option value="<?= $command['comand_id'] ?>" <?= $command['comand_id'] == $playerData['home_team'] ? 'selected' : '' ?>><?= htmlspecialchars($command['name_comand']) ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p><input type="text" class="form-control fs-5" name="away_team" value="<?= htmlspecialchars($playerData['away_team']) ?>" required></p>
            <p><input type="number" class="form-control fs-5" name="score_home_team" value="<?= htmlspecialchars($playerData['score_home_team']) ?>" required></p>
            <p><input type="number" class="form-control fs-5" name="score_away_team" value="<?= htmlspecialchars($playerData['score_away_team']) ?>" required></p>
            <p><input type="file" class="form-control fs-5" name="img_match"></p>
            <p><input type="number" class="form-control fs-5" name="price_ticket" value="<?= htmlspecialchars($playerData['price_ticket']) ?>" required></p>
            <input type="hidden" name="old_img_player" value="<?= htmlspecialchars($playerData['img_match']) ?>">
            <input type="hidden" name="Hid" value="<?= $playerData['id_match'] ?>">
            <p><button type="submit" name="submitUpdate" class="form-control btn btn-success fs-5">&#128190; Сохранить изменения</button></p>
        </form>
    </div>
    <?php endif; ?>

    <h3>ВСЕ МАТЧИ</h3>
    <hr>
    <div class="table-scroll-wrap">
    <table class="table fs-6">
        <thead class="table-success fs-5">
            <tr>
                <th>№</th>
                <th>Название</th>
                <th>Описание</th>
                <th>Фото</th>
                <th>Цена</th>
                <th>Дата</th>
                <th>Время</th>
                <th>Д команда</th>
                <th>Г команда</th>
                <th>Д счёт</th>
                <th>Г счёт</th>
                <th colspan="2">Действия</th>
            </tr>
        </thead>
        <?php
        $tabPlayer = $player->getAll();
        while ($row = $tabPlayer->fetch()) {
            $homeTeamName = $row['home_team'];
            foreach ($commands as $command) {
                if ($command['comand_id'] == $row['home_team']) {
                    $homeTeamName = $command['name_comand'];
                    break;
                }
            }
        ?>
            <tr>
                <td><?= $row['id_match'] ?></td>
                <td><?= $row['title_match'] ?></td>
                <td><span class="cell-text-clamp" title="<?= htmlspecialchars($row['description_match']) ?>"><?= htmlspecialchars($row['description_match']) ?></span></td>
                <td><img src="<?= $row['img_match'] ?>" alt="Фото матча" width="100"></td>
                <td><?= htmlspecialchars($row['price_ticket']) ?>₽</td>
                <td><?= $row['date_match'] ?></td>
                <td><?= $row['time_match'] ?></td>
                <td><?= htmlspecialchars($homeTeamName) ?></td>
                <td><?= $row['away_team'] ?></td>
                <td><?= $row['score_home_team'] ?></td>
                <td><?= $row['score_away_team'] ?></td>
                <td>
                    <form action="#" method="post">
                        <button type="submit" name="submitEdit" class="btn text-bg-success" value="<?= $row['id_match'] ?>">
                            &#128397; Редактировать
                        </button>
                    </form>
                </td>
                <td>
                    <form action="#" method="post">
                        <button type="submit" name="submitDelete" class="btn text-bg-danger" value="<?= $row['id_match'] ?>">
                            &#128465; Удалить
                        </button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
    </div>
</div>
</div>

<script>
    const addToggleButton = document.getElementById('addToggleButton');
    const addFormSection = document.getElementById('addFormSection');

    if (addToggleButton && addFormSection) {
        addToggleButton.addEventListener('click', () => {
            addFormSection.classList.toggle('active');
            addToggleButton.textContent = addFormSection.classList.contains('active')
                ? 'Скрыть форму добавления'
                : 'Добавить матч';
        });
    }
</script>

<?php require_once 'assets/footer.php'; ?>
