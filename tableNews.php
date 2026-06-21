<?php
require_once 'assets/menuAdmin.php';
require_once './assets/classes/dbd.php';
require_once './assets/classes/News.php';

$dbh = new Dbh();
$db = $dbh->connect_pdo();

$player = new News($db);

$showAddForm = isset($_POST['addPlayer']);
$showEditForm = isset($_POST['submitEdit']);
$playerData = null;
$alertHtml = '';

if (isset($_POST['submitEdit'])) {
    $playerData = $player->readName($_POST['submitEdit']);
}

if (isset($_POST['addPlayer'])) {
    $player->title_news = $_POST['title_news'];
    $player->content_news = $_POST['content_news'];
    $player->date_published = $_POST['date_published'];
    $player->author_news = $_POST['author_news'];

    $img = $_FILES['img_news']['name'];
    if ($img) {
        $target_dir = "assets/img/";
        $target_file = $target_dir . basename($img);
        move_uploaded_file($_FILES['img_news']['tmp_name'], $target_file);
        $player->img_news = $target_file;
    } else {
        $player->img_news = 'default.jpg';
    }

    if ($player->proverka($player->title_news) != 0) {
        $alertHtml = '<div class="alert alert-danger">Такая новость уже есть!</div>';
    } elseif ($player->create()) {
        echo '<div class="alert alert-success">Новость добавлена!</div>';
        echo "<meta http-equiv='refresh' content='5'>";
        $showAddForm = false;
    } else {
        $alertHtml = '<div class="alert alert-warning">Ошибка при добавлении.</div>';
    }
}

if (isset($_POST['submitUpdate'])) {
    $player->title_news = $_POST['title_news'];
    $player->content_news = $_POST['content_news'];
    $player->date_published = $_POST['date_published'];
    $player->author_news = $_POST['author_news'];

    $img = $_FILES['img_news']['name'];
    if ($img) {
        $target_dir = "assets/img/";
        $target_file = $target_dir . basename($img);
        move_uploaded_file($_FILES['img_news']['tmp_name'], $target_file);
        $player->img_news = $target_file;
    } else {
        $player->img_news = $_POST['old_img_player'];
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
    $news_id = $_POST['submitDelete'];

    if ($player->delete($news_id)) {
        echo '<div class="alert alert-success">Новость удалена!</div>';
        echo "<meta http-equiv='refresh' content='2'>";
    } else {
        $alertHtml = '<div class="alert alert-danger">Ошибка при удалении новости.</div>';
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
        <?= $showAddForm ? 'Скрыть форму добавления' : 'Добавить новость' ?>
    </button>

    <div id="addFormSection" class="add-form-section<?= $showAddForm ? ' active' : '' ?>">
        <h3>ДОБАВЛЕНИЕ НОВОСТИ</h3>
        <hr>
        <?php if (!empty($alertHtml) && isset($_POST['addPlayer'])) echo $alertHtml; ?>
        <form action="" method="post" class="fs-4" enctype="multipart/form-data">
            <p><input type="text" class="form-control fs-5" name="title_news" placeholder="Заголовок новости" value="<?= htmlspecialchars($_POST['title_news'] ?? '') ?>" required></p>
            <p><textarea class="form-control fs-5" name="content_news" placeholder="Текст новости" rows="5" required><?= htmlspecialchars($_POST['content_news'] ?? '') ?></textarea></p>
            <p><input type="date" class="form-control fs-5" name="date_published" value="<?= htmlspecialchars($_POST['date_published'] ?? '') ?>" required></p>
            <p><input type="text" class="form-control fs-5" name="author_news" placeholder="Автор" value="<?= htmlspecialchars($_POST['author_news'] ?? '') ?>" required></p>
            <p><input type="file" class="form-control fs-5" name="img_news" accept="image/*"></p>
            <p><button type="submit" name="addPlayer" class="form-control btn btn-success fs-5">&#128190; Добавить новость</button></p>
        </form>
    </div>

    <?php if ($showEditForm && $playerData): ?>
    <div class="edit-form-section active">
        <h3>РЕДАКТИРОВАНИЕ НОВОСТИ</h3>
        <hr>
        <?php if (!empty($alertHtml) && isset($_POST['submitUpdate'])) echo $alertHtml; ?>
        <form action="" method="post" class="fs-4" enctype="multipart/form-data">
            <p><input type="text" class="form-control fs-5" name="title_news" value="<?= htmlspecialchars($playerData['title_news']) ?>" required></p>
            <p><input type="text" class="form-control fs-5" name="content_news" value="<?= htmlspecialchars($playerData['content_news']) ?>" required></p>
            <p><input type="text" class="form-control fs-5" name="date_published" value="<?= htmlspecialchars($playerData['date_published']) ?>" required></p>
            <p><input type="text" class="form-control fs-5" name="author_news" value="<?= htmlspecialchars($playerData['author_news']) ?>" required></p>
            <p><input type="file" class="form-control fs-5" name="img_news"></p>
            <input type="hidden" name="Hid" value="<?= $playerData['news_id'] ?>">
            <p><button type="submit" name="submitUpdate" class="form-control btn btn-success fs-5">&#128190; Сохранить изменения</button></p>
        </form>
    </div>
    <?php endif; ?>

    <h3>ВСЕ НОВОСТИ</h3>
    <hr>
    <div class="table-scroll-wrap">
    <table class="table fs-6">
        <thead class="table-success fs-5">
            <tr>
                <th>№</th>
                <th>Заголовок</th>
                <th>Текст</th>
                <th>Фото</th>
                <th>Дата</th>
                <th>Автор</th>
                <th colspan="2">Действия</th>
            </tr>
        </thead>
        <?php
        $tabPlayer = $player->getAll();
        while ($row = $tabPlayer->fetch()) {
        ?>
            <tr>
                <td><?= $row['news_id'] ?></td>
                <td><?= $row['title_news'] ?></td>
                <td><span class="cell-text-clamp" title="<?= htmlspecialchars($row['content_news']) ?>"><?= htmlspecialchars($row['content_news']) ?></span></td>
                <td><img src="<?= $row['img_news'] ?>" alt="Фото новости" width="100"></td>
                <td><?= $row['date_published'] ?></td>
                <td><?= $row['author_news'] ?></td>
                <td>
                    <form action="#" method="post">
                        <button type="submit" name="submitEdit" class="btn text-bg-success" value="<?= $row['news_id'] ?>">
                            &#128397; Редактировать
                        </button>
                    </form>
                </td>
                <td>
                    <form action="#" method="post">
                        <button type="submit" name="submitDelete" class="btn text-bg-danger" value="<?= $row['news_id'] ?>">
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
                : 'Добавить новость';
        });
    }
</script>

<?php require_once 'assets/footer.php'; ?>
