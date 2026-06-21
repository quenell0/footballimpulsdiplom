<?php
ob_start();
session_start();
require_once './assets/classes/dbd.php';
$db = new Dbh();
$pdo = $db->connect_pdo();

require_once 'blade/header.php';

if (isset($_SESSION['user_login'])) {
    if ($_SESSION['user_role'] === 'admin') {
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
$stmt = $pdo->prepare("SELECT * FROM News WHERE news_id = ?");
$stmt->execute([$id]);
$news = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$news) {
    echo "<h2 style='color:white;text-align:center;'>Новость не найдена.</h2>";
    require_once 'blade/footer.php';
    exit();
}

if (isset($_POST['submit_comment']) && isset($_SESSION['user_id'])) {
    $comment_content = trim($_POST['comment_content']);
    $author_id = $_SESSION['user_id'];

    if (!empty($comment_content)) {
        $stmt = $pdo->prepare('INSERT INTO Comment (news_id, author_comment, content_comment, date_posted) VALUES (?, ?, ?, NOW())');
        $stmt->execute([$id, $author_id, $comment_content]);
        header("Location: newspage.php?id=$id");
        exit();
    } else {
        echo "<p style='color: red;'>Комментарий не может быть пустым.</p>";
    }
}

if (isset($_POST['delete_comment']) && isset($_SESSION['user_id'])) {
    $comment_id = (int) $_POST['delete_comment'];
    $is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

    $stmt = $pdo->prepare('SELECT author_comment, news_id FROM Comment WHERE comment_id = ?');
    $stmt->execute([$comment_id]);
    $commentToDelete = $stmt->fetch(PDO::FETCH_ASSOC);

    if (
        $commentToDelete
        && (int) $commentToDelete['news_id'] === (int) $id
        && ($is_admin || (int) $commentToDelete['author_comment'] === (int) $_SESSION['user_id'])
    ) {
        $stmt = $pdo->prepare('DELETE FROM Comment WHERE comment_id = ?');
        $stmt->execute([$comment_id]);
    }

    header("Location: newspage.php?id=$id");
    exit();
}

?>

<a href="javascript:history.back()" class="back-button">← Назад</a>

<div class="page-layout">

            <!-- Левая колонка -->
            <div class="related-column left">
        <?php
        $stmt_left = $pdo->prepare("SELECT * FROM News WHERE news_id != ? ORDER BY date_published DESC LIMIT 3");
        $stmt_left->execute([$id]);
        $left_news = $stmt_left->fetchAll(PDO::FETCH_ASSOC);

        foreach ($left_news as $row): ?>
            <div class="news-card">
                <a class="news-card-btn" href="newspage.php?id=<?= $row['news_id'] ?>">
                    <img class="image-news" src="<?= htmlspecialchars($row['img_news']) ?>" alt="Новость">
                    <div class="news-content">
                        <h3><?= htmlspecialchars($row['title_news']) ?></h3>
                        <p><?= nl2br(htmlspecialchars(mb_strimwidth($row['content_news'], 0, 80, '...'))) ?></p>
                        <small><?= htmlspecialchars($row['author_news']) ?> | <?= htmlspecialchars($row['date_published']) ?></small>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Основная статья -->
    <div class="article-container">
        <h1 class="article-title"><?= htmlspecialchars($news['title_news']) ?></h1>
        <img class="article-image" src="<?= htmlspecialchars($news['img_news']) ?>" alt="Изображение новости">
        <div class="article-meta"><?= htmlspecialchars($news['date_published']) ?> | <?= htmlspecialchars($news['author_news']) ?></div>
        <div class="article-content"><?= nl2br(htmlspecialchars($news['content_news'])) ?></div>

        <?php if (isset($_SESSION['user_id'])): ?>

<!-- Комментарии -->
<hr style="margin: 40px 0; border: 1px solid gray;">
<h2>Комментарии</h2>

<form method="post" style="margin-bottom: 30px;">
    <textarea id="commentBox" name="comment_content" oninput="autoResize(this)" style="width: 100%; padding: 10px; background-color: #111; color: white; border: 1px solid #333; border-radius: 5px; resize: none; overflow: hidden; min-height: 40px; font-family: inherit; font-size: 14px;" placeholder="Оставьте комментарий" required></textarea>
    <script>
        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = (textarea.scrollHeight) + 'px';
        }
    </script>
    <button type="submit" name="submit_comment" style="margin-top:10px; padding:10px 20px; background-color: rgb(0,192,235); border:none; color:white; border-radius:5px; cursor:pointer;">
        Отправить
    </button>
</form>

<?php else: ?>
    <p><a href="login.php" style="color:rgb(0,192,235);">Войдите, чтобы оставить комментарий.</a></p>
<?php endif; ?>


        <?php
        $stmt = $pdo->prepare("SELECT Comment.*, User.username, User.foto FROM Comment JOIN User ON Comment.author_comment = User.user_id WHERE Comment.news_id = ? ORDER BY date_posted DESC");
        $stmt->execute([$id]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($comments):
            foreach ($comments as $comment):
                $canDeleteComment = isset($_SESSION['user_id']) && (
                    (int) $comment['author_comment'] === (int) $_SESSION['user_id']
                    || (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin')
                );
            ?>
                <div style="margin-bottom: 20px; background-color: #1d1d1d; padding: 15px; border-radius: 10px;">
                    <div style="display: flex; align-items: center; margin-bottom: 10px; gap: 10px;">
                        <?php if (!empty($comment['foto'])): ?>
                            <img src="<?= htmlspecialchars($comment['foto']) ?>" alt="Аватар пользователя" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                        <?php else: ?>
                            <div style="width:40px; height:40px; border-radius:50%; background-color:#555; display:flex; align-items:center; justify-content:center; color:#ccc;">?</div>
                        <?php endif; ?>
                        <strong><?= htmlspecialchars($comment['username']) ?></strong>
                        <span style="margin-left:auto; color:gray; font-size: 12px;"><?= htmlspecialchars($comment['date_posted'] ?? '') ?></span>
                        <?php if ($canDeleteComment): ?>
                            <form method="post" style="margin: 0;" onsubmit="return confirm('Удалить этот комментарий?');">
                                <input type="hidden" name="delete_comment" value="<?= (int) $comment['comment_id'] ?>">
                                <button type="submit" style="padding: 4px 10px; background: rgba(220, 50, 50, 0.2); border: 1px solid rgba(220, 50, 50, 0.45); color: #ff8888; border-radius: 6px; cursor: pointer; font-size: 12px;">
                                    Удалить
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <div style="color: #eee; font-size: 15px;">
                        <?= nl2br(htmlspecialchars($comment['content_comment'] ?? '')) ?>
                    </div>
                </div>
            <?php endforeach;
        else:
            echo "<p>Комментариев пока нет.</p>";
        endif;
        ?>
    </div>

            <!-- Правая колонка -->
            <div class="related-column right">
        <?php
        $stmt_right = $pdo->prepare("SELECT * FROM News WHERE news_id != ? ORDER BY date_published DESC LIMIT 3 OFFSET 3");
        $stmt_right->execute([$id]);
        $right_news = $stmt_right->fetchAll(PDO::FETCH_ASSOC);

        foreach ($right_news as $row): ?>
            <div class="news-card">
                <a class="news-card-btn" href="newspage.php?id=<?= $row['news_id'] ?>">
                    <img class="image-news" src="<?= htmlspecialchars($row['img_news']) ?>" alt="Новость">
                    <div class="news-content">
                        <h3><?= htmlspecialchars($row['title_news']) ?></h3>
                        <p><?= nl2br(htmlspecialchars(mb_strimwidth($row['content_news'], 0, 80, '...'))) ?></p>
                        <small><?= htmlspecialchars($row['date_published']) ?> | <?= htmlspecialchars($row['author_news']) ?></small>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<?php require_once 'blade/footer.php'; ?>

<?php
ob_end_flush();
?>