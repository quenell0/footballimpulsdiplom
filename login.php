<?php
require_once 'assets/classes/dbd.php';
session_start();

$db = new Dbh();
$errorMsg = [];

if (isset($_REQUEST['btn_login'])) {
    $username_email = strip_tags($_REQUEST["txt_username_email"]);
    $password = strip_tags($_REQUEST["txt_password"]);

    if (empty($username_email)) {
        $errorMsg[] = "Пожалуйста, введите имя пользователя или адрес электронной почты";
    } elseif (empty($password)) {
        $errorMsg[] = "Пожалуйста, введите пароль";
    } else {
        try {
            $pdo = $db->connect_pdo();

            if (!$pdo) {
                throw new Exception("Не удалось подключиться к базе данных.");
            }

            $select_stmt = $pdo->prepare("SELECT * FROM User WHERE username=:uname OR email=:uemail");
            $select_stmt->execute([':uname' => $username_email, ':uemail' => $username_email]);

            $row = $select_stmt->fetch(PDO::FETCH_ASSOC);

            if ($select_stmt->rowCount() > 0) {
                if (password_verify($password, $row["password"])) {
                    $_SESSION["user_login"] = $row["user_id"];
                    $_SESSION["user_id"] = $row["user_id"];
                    $_SESSION["user_role"] = $row["role"];
                    $_SESSION["user_avatar"] = $row["foto"];

                    header("Location: welcome.php");
                    exit;
                } else {
                    $errorMsg[] = "Неверный пароль";
                }
            } else {
                $errorMsg[] = "Неправильное имя пользователя или адрес электронной почты";
            }
        } catch (PDOException $e) {
            $errorMsg[] = "Ошибка базы данных: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="style/style.css?v=<?php echo filemtime('style/style.css'); ?>">
</head>
<body class="site-body auth-page">
<?php
if (isset($_SESSION['user_login'])) {
    $role = $_SESSION['user_role'] ?? 'user';
    require_once $role === 'admin' ? 'blade/menuadmin.php' : 'blade/menuuser.php';
} else {
    require_once 'blade/menu.php';
}
?>
<div class="wrapper">
<div class="container">
    <h1>Авторизация</h1>

    <?php
    foreach ($errorMsg as $error) {
        echo '<div class="alert alert-danger">' . $error . '</div>';
    }
    ?>

    <form method="post" class="form-horizontal">
        <div class="form-group">
            <label class="col-sm-3 control-label">Имя пользователя или Email</label>
            <div class="col-sm-6">
                <input type="text" name="txt_username_email" class="form-control" placeholder="Введите имя или email">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">Пароль</label>
            <div class="col-sm-6">
                <input type="password" name="txt_password" class="form-control" placeholder="Введите пароль">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
                <input type="submit" name="btn_login" class="btn register-btn" value="Войти">
            </div>
        </div>
        <div class="form-group">
            <div class="login col-sm-offset-3 col-sm-9">
                Нет учетной записи? <a href="register.php">Зарегистрироваться</a>
            </div>
        </div>
    </form>
</div>
</div>
<?php require_once 'blade/footer.php'; ?>
</body>
</html>  