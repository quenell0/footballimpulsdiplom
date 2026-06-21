<?php
require_once 'assets/classes/dbd.php';
session_start();

$db = new Dbh();
$errorMsg = [];
$registerMsg = '';
$default_avatar = 'assets/img/default_avatar.png';

try {
    $pdo = $db->connect_pdo();

    if (!$pdo) {
        throw new Exception("Не удалось подключиться к базе данных.");
    }

    if (isset($_POST['btn_register'])) {

        $username = strip_tags(trim($_POST['txt_uname']));
        $email = strip_tags(trim($_POST['txt_email']));
        $password = strip_tags(trim($_POST['txt_pass']));

        $foto = $default_avatar;

        // Загрузка аватара
        if (isset($_FILES['txt_foto']) && $_FILES['txt_foto']['error'] === UPLOAD_ERR_OK) {
            $file_name = $_FILES['txt_foto']['name'];
            $file_tmp = $_FILES['txt_foto']['tmp_name'];
            $file_size = $_FILES['txt_foto']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed = ["jpeg", "jpg", "png"];

            if (!in_array($file_ext, $allowed)) {
                $errorMsg[] = "Разрешены только JPEG и PNG файлы.";
            } elseif ($file_size > 2097152) {
                $errorMsg[] = "Размер изображения не должен превышать 2MB.";
            } else {
                $upload_dir = "assets/img/avatars/";
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                $foto_name = uniqid('avatar_', true) . '.' . $file_ext;
                $foto_path = $upload_dir . $foto_name;

                if (move_uploaded_file($file_tmp, $foto_path)) {
                    $foto = $foto_path;
                } else {
                    $errorMsg[] = "Не удалось загрузить изображение.";
                }
            }
        }

        if (empty($username)) $errorMsg[] = "Введите имя пользователя.";
        if (empty($email)) $errorMsg[] = "Введите Email.";
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errorMsg[] = "Некорректный формат Email.";
        if (empty($password)) $errorMsg[] = "Введите пароль.";
        elseif (strlen($password) < 6) $errorMsg[] = "Пароль должен быть не менее 6 символов.";

        if (empty($errorMsg)) {
            $stmt = $pdo->prepare("SELECT username, email FROM User WHERE username=:uname OR email=:uemail");
            $stmt->execute([':uname' => $username, ':uemail' => $email]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                if ($row['username'] === $username) $errorMsg[] = "Имя пользователя уже занято.";
                if ($row['email'] === $email) $errorMsg[] = "Email уже зарегистрирован.";
            } else {
                $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
                $insert = $pdo->prepare("INSERT INTO User (username, email, password, role, foto) VALUES (:uname, :uemail, :upass, :urole, :ufoto)");
                $executed = $insert->execute([
                    ':uname' => $username,
                    ':uemail' => $email,
                    ':upass' => $hashed_pass,
                    ':urole' => 'user',
                    ':ufoto' => $foto
                ]);

                if ($executed) {
                    $registerMsg = "Регистрация прошла успешно! Перенаправление...";
                    header("refresh:5;login.php");
                } else {
                    $errorMsg[] = "Ошибка при регистрации.";
                }
            }
        }
    }
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
    error_log("Ошибка регистрации: " . $e->getMessage());
}
?>

<?php
// Меню будет подключено внутри <body> после head — см. ниже
// (раньше было до DOCTYPE и это ломало структуру HTML)
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
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
    <h1>Регистрация</h1>

    <?php
    foreach ($errorMsg as $error) {
        echo '<div class="alert alert-danger">' . $error . '</div>';
    }

    if ($registerMsg) {
        echo '<div class="alert alert-success">' . $registerMsg . '</div>';
    }
    ?>

    <form method="post" class="form-horizontal" enctype="multipart/form-data">
        <div class="form-group">
            <label class="col-sm-3 control-label">Имя пользователя</label>
            <div class="col-sm-6">
                <input type="text" name="txt_uname" class="form-control" placeholder="Введите имя пользователя">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">Email</label>
            <div class="col-sm-6">
                <input type="email" name="txt_email" class="form-control" placeholder="Введите Email">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">Пароль</label>
            <div class="col-sm-6">
                <input type="password" name="txt_pass" class="form-control" placeholder="Введите пароль">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">Аватар</label>
            <div class="col-sm-6">
                <input type="file" name="txt_foto" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
                <input type="submit" name="btn_register" class="btn register-btn" value="Зарегистрироваться">
            </div>
        </div>
        <div class="form-group">
            <div class="login col-sm-offset-3 col-sm-9">
                Уже зарегистрированы? <a href="login.php">Войти</a>
            </div>
        </div>
    </form>
</div>
</div>
<?php require_once 'blade/footer.php'; ?>
</body>
</html>
