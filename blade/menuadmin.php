   <?php
   require_once 'assets/classes/dbd.php';


   $connect = new Dbh();
   $db = $connect->connect_pdo();
   ?>

<nav class="site-nav">
    <button class="site-nav__toggle" type="button" aria-expanded="false" aria-label="Открыть меню">
        <span></span>
        <span></span>
        <span></span>
    </button>
    <ul>
        <li><a href="index.php"><img src="./assets/img/logo_impuls.png" alt="" class="logo"></a></li>

        <li><a href="index.php">Главная</a></li>
        <li><a href="news.php">Новости</a></li>
        <li><a href="matches.php">Матчи</a></li>
        <li class="dropdown-info"><a href="">Информация</a>
            <ul>
                <li><a href="info_about.php">О нас</a></li>
                <li><a href="info_teams.php">Команды</a></li>
                <li><a href="players.php">Футболисты</a></li>
            </ul>
        </li>
        <li><a href="stadiums.php">Стадионы</a></li>
        <li><a href="welcome.php">Профиль</a></li>
        <li><a href="indexAdmin.php">Админка</a></li>
        <li><a href="logout.php">Выход</a></li>
    </ul>
</nav>

