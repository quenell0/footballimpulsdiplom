<?php
require_once 'assets/menuAdmin.php';
require_once './assets/classes/dbd.php';
require_once './assets/classes/Players.php';

$dbh = new Dbh();
$db = $dbh->connect_pdo();

$player = new Player($db);
$commands = $db->query("SELECT comand_id, name_comand FROM Comands ORDER BY name_comand ASC")->fetchAll(PDO::FETCH_ASSOC);
$positions = $db->query("SELECT position_id, name_position FROM Position ORDER BY name_position ASC")->fetchAll(PDO::FETCH_ASSOC);

$showAddForm = isset($_POST['addPlayer']);
$showEditForm = isset($_POST['submitEdit']);
$playerData = null;
$alertHtml = '';

if (isset($_POST['submitEdit'])) {
    $playerData = $player->readName($_POST['submitEdit']);
}

if (isset($_POST['addPlayer'])) {
    $player->name_player = $_POST['name_player'];
    $player->description_player = $_POST['description_player'];
    $player->position = (int) $_POST['position'];
    $player->age_player = $_POST['age_player'];
    $player->nationality_player = $_POST['nationality_player'];
    $player->comand_id = !empty($_POST['comand_id']) ? (int) $_POST['comand_id'] : null;

    $img = $_FILES['img_player']['name'];
    if ($img) {
        $target_dir = "assets/img/";
        $target_file = $target_dir . basename($img);
        move_uploaded_file($_FILES['img_player']['tmp_name'], $target_file);
        $player->img_player = $target_file;
    } else {
        $player->img_player = 'default.jpg';
    }

    if ($player->proverka($player->name_player) != 0) {
        $alertHtml = '<div class="alert alert-danger">Такой футболист уже есть!</div>';
    } elseif ($player->create()) {
        echo '<div class="alert alert-success">Футболист добавлен!</div>';
        echo "<meta http-equiv='refresh' content='5'>";
        $showAddForm = false;
    } else {
        $alertHtml = '<div class="alert alert-warning">Ошибка при добавлении.</div>';
    }
}

if (isset($_POST['submitUpdate'])) {
    $player->name_player = $_POST['name_player'];
    $player->description_player = $_POST['description_player'];
    $player->position = (int) $_POST['position'];
    $player->age_player = $_POST['age_player'];
    $player->nationality_player = $_POST['nationality_player'];
    $player->comand_id = !empty($_POST['comand_id']) ? (int) $_POST['comand_id'] : null;

    $img = $_FILES['img_player']['name'];
    if ($img) {
        $target_dir = "assets/img/";
        $target_file = $target_dir . basename($img);
        move_uploaded_file($_FILES['img_player']['tmp_name'], $target_file);
        $player->img_player = $target_file;
    } else {
        $player->img_player = $_POST['old_img_player'];
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
    $player_id = $_POST['submitDelete'];

    if ($player->delete($player_id)) {
        echo '<div class="alert alert-success">Футболист удален!</div>';
        echo "<meta http-equiv='refresh' content='2'>";
    } else {
        $alertHtml = '<div class="alert alert-danger">Ошибка при удалении футболиста.</div>';
    }
}
?>

<style>
.admin-table-wrap {
    margin: 30px;
}

.country-table-full {
    background: rgba(30,30,40,0.95);
    border-radius: 20px;
    padding: 25px;
    border: 1px solid rgba(56,189,248,0.2);
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

.form-control.description-field {
    min-height: 220px;
    resize: vertical;
    line-height: 1.5;
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
        <?= $showAddForm ? 'Скрыть форму добавления' : 'Добавить футболиста' ?>
    </button>

    <div id="addFormSection" class="add-form-section<?= $showAddForm ? ' active' : '' ?>">
        <h3>ДОБАВЛЕНИЕ ФУТБОЛИСТА</h3>
        <hr>
        <?php if (!empty($alertHtml) && isset($_POST['addPlayer'])) echo $alertHtml; ?>
        <form action="" method="post" class="fs-4" enctype="multipart/form-data">
            <p><input type="text" class="form-control fs-5" name="name_player" placeholder="Имя футболиста" value="<?= htmlspecialchars($_POST['name_player'] ?? '') ?>" required></p>
            <p><textarea class="form-control fs-5 description-field" name="description_player" placeholder="Описание / биография" rows="10" required><?= htmlspecialchars($_POST['description_player'] ?? '') ?></textarea></p>
            <p>
                <select name="position" class="form-control fs-5" required>
                    <option value="">Выберите позицию</option>
                    <?php foreach ($positions as $pos): ?>
                        <option value="<?= $pos['position_id'] ?>" <?= (($_POST['position'] ?? '') == $pos['position_id']) ? 'selected' : '' ?>><?= htmlspecialchars($pos['name_position']) ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p><input type="number" class="form-control fs-5" name="age_player" placeholder="Возраст" value="<?= htmlspecialchars($_POST['age_player'] ?? '') ?>" required></p>
            <p><input type="text" class="form-control fs-5" name="nationality_player" placeholder="Национальность" value="<?= htmlspecialchars($_POST['nationality_player'] ?? '') ?>" required></p>
            <p>
                <select name="comand_id" class="form-control fs-5">
                    <option value="">Без команды</option>
                    <?php foreach ($commands as $command): ?>
                        <option value="<?= $command['comand_id'] ?>" <?= (($_POST['comand_id'] ?? '') == $command['comand_id']) ? 'selected' : '' ?>><?= htmlspecialchars($command['name_comand']) ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p><input type="file" class="form-control fs-5" name="img_player" accept="image/*"></p>
            <p><button type="submit" name="addPlayer" class="form-control btn btn-success fs-5">&#128190; Добавить футболиста</button></p>
        </form>
    </div>

    <?php if ($showEditForm && $playerData): ?>
    <div class="edit-form-section active">
        <h3>РЕДАКТИРОВАНИЕ ФУТБОЛИСТА</h3>
        <hr>
        <?php if (!empty($alertHtml) && isset($_POST['submitUpdate'])) echo $alertHtml; ?>
        <form action="" method="post" class="fs-4" enctype="multipart/form-data">
            <p><input type="text" class="form-control fs-5" name="name_player" value="<?= htmlspecialchars($playerData['name_player']) ?>" required></p>
            <p><textarea class="form-control fs-5 description-field" name="description_player" rows="10" required><?= htmlspecialchars($playerData['description_player']) ?></textarea></p>
            <p>
                <select name="position" class="form-control fs-5" required>
                    <option value="">Выберите позицию</option>
                    <?php foreach ($positions as $pos): ?>
                        <option value="<?= $pos['position_id'] ?>" <?= ($playerData['position'] ?? '') == $pos['position_id'] ? 'selected' : '' ?>><?= htmlspecialchars($pos['name_position']) ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p><input type="number" class="form-control fs-5" name="age_player" value="<?= htmlspecialchars($playerData['age_player']) ?>" required></p>
            <p><input type="text" class="form-control fs-5" name="nationality_player" value="<?= htmlspecialchars($playerData['nationality_player']) ?>" required></p>
            <p>
                <select name="comand_id" class="form-control fs-5">
                    <option value="">Без команды</option>
                    <?php foreach ($commands as $command): ?>
                        <option value="<?= $command['comand_id'] ?>" <?= ($playerData['comand_id'] ?? '') == $command['comand_id'] ? 'selected' : '' ?>><?= htmlspecialchars($command['name_comand']) ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p><input type="file" class="form-control fs-5" name="img_player"></p>
            <input type="hidden" name="Hid" value="<?= $playerData['player_id'] ?>">
            <p><button type="submit" name="submitUpdate" class="form-control btn btn-success fs-5">&#128190; Сохранить изменения</button></p>
        </form>
    </div>
    <?php endif; ?>

    <h3>ВСЕ ФУТБОЛИСТЫ</h3>
    <hr>
    <div class="table-scroll-wrap">
    <table class="table fs-6">
        <thead class="table-success fs-5">
            <tr>
                <th>№</th>
                <th>Имя</th>
                <th>Фото</th>
                <th>Позиция</th>
                <th>Команда</th>
                <th>Возраст</th>
                <th>Национальность</th>
                <th colspan="2">Действия</th>
            </tr>
        </thead>
        <?php
        $tabPlayer = $player->getAll();
        while ($row = $tabPlayer->fetch()) {
        ?>
            <tr>
                <td><?= $row['player_id'] ?></td>
                <td><?= $row['name_player'] ?></td>
                <td><img src="<?= $row['img_player'] ?>" alt="Фото футболиста" width="100"></td>
                <td><?= htmlspecialchars($row['name_position'] ?? '—') ?></td>
                <td><?= htmlspecialchars($row['team_name'] ?? '—') ?></td>
                <td><?= $row['age_player'] ?></td>
                <td><?= $row['nationality_player'] ?></td>
                <td>
                    <form action="#" method="post">
                        <button type="submit" name="submitEdit" class="btn text-bg-success" value="<?= $row['player_id'] ?>">
                            &#128397; Редактировать
                        </button>
                    </form>
                </td>
                <td>
                    <form action="#" method="post">
                        <button type="submit" name="submitDelete" class="btn text-bg-danger" value="<?= $row['player_id'] ?>">
                            &#128465; Удалить
                        </button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
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
                : 'Добавить футболиста';
        });
    }
</script>

<?php require_once 'assets/footer.php'; ?>
