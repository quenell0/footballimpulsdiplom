<?php
// СЕССИЯ ДОЛЖНА БЫТЬ В САМОМ НАЧАЛЕ, ДО ЛЮБОГО ВЫВОДА HTML!
session_start();
require_once './assets/classes/dbd.php';

$connect = new Dbh(); 
$db = $connect->connect_pdo();

// Получаем информацию о текущем админе (если есть сессия)
$adminName = isset($_SESSION['user_login']) ? $_SESSION['user_login'] : 'Администратор';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css?v=<?php echo filemtime(__DIR__ . '/../style/style.css'); ?>">
    <title>Админка Импульс | Управление клубом</title>
</head>
<body class="admin-site">

<div class="admin-header">
    <h1>
        <a href="../../index.php"><i class="fas fa-arrow-left"></i> На главную</a>
        <span><i class="fas fa-crown"></i> Импульс Админ-панель</span>
    </h1>
    <div class="user-info">
            <i class="fas fa-user-shield"></i>
            <span><?= htmlspecialchars($adminName) ?></span>
            <a href="welcome.php?logout=true" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Выход</a>
        </div>
    </div>

    <nav class="admin-top-nav">
        <a href="tableNews.php"><i class="fas fa-newspaper"></i> Новости</a>
        <a href="tableMatches.php"><i class="fas fa-futbol"></i> Матчи</a>
        <a href="tablePlayers.php"><i class="fas fa-users"></i> Футболисты</a>
        <a href="tableTeams.php"><i class="fas fa-shield-alt"></i> Команды</a>
        <a href="tableStadiums.php"><i class="fas fa-home"></i> Стадионы</a>
        <a href="indexAdmin.php"><i class="fas fa-tachometer-alt"></i> Дашборд</a>
    </nav>

    <div class="admin-layout">
        <!-- БОКОВОЕ МЕНЮ -->
        <div class="admin-sidebar">
            <div class="sidebar-section">
                <h3><i class="fas fa-database"></i> <span>Управление</span></h3>
                <ul class="sidebar-menu">
                    <li><a href="tableNews.php"><i class="fas fa-newspaper"></i> <span>Новости</span></a></li>
                    <hr>
                    <li><a href="tableMatches.php"><i class="fas fa-futbol"></i> <span>Матчи</span></a></li>
                    <hr>
                    <li><a href="tablePlayers.php"><i class="fas fa-users"></i> <span>Футболисты</span></a></li>
                    <hr>
                    <li><a href="tableTeams.php"><i class="fas fa-shield-alt"></i> <span>Команды</span></a></li>
                    <hr>
                    <li><a href="tableStadiums.php"><i class="fas fa-home"></i> <span>Стадионы</span></a></li>
                </ul>
            </div>
            
            <div class="sidebar-section">
                <h3><i class="fas fa-chart-line"></i> <span>Статистика</span></h3>
                <ul class="sidebar-menu">
                    <li><a href="indexAdmin.php"><i class="fas fa-tachometer-alt"></i> <span>Дашборд</span></a></li>
                </ul>
            </div>
        </div>

    <!-- ОСНОВНОЙ КОНТЕНТ -->
    <div class="admin-main">