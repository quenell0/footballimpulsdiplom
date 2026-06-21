<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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

<div class="club-contain">
    <div class="club">
        <h1><b>ФУТБОЛЬНЫЙ КЛУБ «Импульс»</b></h1>
        <p>Организация, ориентированная на развитие и успех, с целью достижения высоких результатов на всех уровнях футбольных турниров. Мы фокусируемся на профессионализме, обучении и поддержке молодых игроков.</p>
        <a class="about-btn" href="info_about.php">Подробнее</a>
    </div>
</div>

<div class="container">
    <div class="news-shap">
    <h1><b>НОВОСТИ</b></h1>
    <a class="all-news" href="news.php">Смотреть все</a>
    </div>
    <div class="news-slider">
        <?php
        $stmt = $pdo->query("SELECT * FROM News ORDER BY date_published DESC LIMIT 6");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<div class='news-card'>";
            echo '<a class="news-card-btn" href="newspage.php?id=' . $row['news_id'] . '">';
            echo '<img class="image-news" src="' . htmlspecialchars($row['img_news']) . '" alt="Изображение новости">';
            echo "<div class='news-content'>";
            echo "<h3>" . htmlspecialchars($row['title_news']) . "</h3>";
            echo "<p>" . nl2br(htmlspecialchars(mb_strimwidth($row['content_news'], 0, 100, '...'))) . "</p>";
            echo "<small>Автор: " . htmlspecialchars($row['author_news']) . " | " . htmlspecialchars($row['date_published']) . "</small>";
            echo "</div>";
            echo '</a>';
            echo "</div>";
        }
        ?>
    </div>
</div>

<div class="container">
<div class="team-contain">
    <div class="team-info">
        <h1><b>«ИМПУЛЬС»</b></h1>
        <p>"Импульс" — это главная команда футбольного клуба Импульс, символ чести, решимости и новаторства. Название отражает силу, скрытую в глубине, и холодный, расчётливый подход к каждой игре.</p>
        <a class="about-btn" href="teamspage.php?id=1">Подробнее</a>
    </div>
    <div class="team-image">
        <img src="assets/img/logo_impuls.png" alt="Иконка команды Импульс">
    </div>
</div>
</div>

<div class="container">
    <div class="news-shap">
    <h1><b>МАТЧИ</b></h1>
    <a class="all-news" href="news.php">Смотреть все</a>
    </div>
    <div class="news-slider">
        <?php
        $stmt = $pdo->query("SELECT * FROM Matches ORDER BY date_match DESC LIMIT 6");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<div class='news-card'>";
            echo '<a class="news-card-btn" href="matchpage.php?id=' . $row['id_match'] . '">';
            echo '<img class="image-news" src="' . htmlspecialchars($row['img_match']) . '" alt="Изображение матча">';
            echo "<div class='news-content'>";
            echo "<h3>" . htmlspecialchars($row['title_match']) . "</h3>";
            echo "<p>" . nl2br(htmlspecialchars(mb_strimwidth($row['description_match'], 0, 100, '...'))) . "</p>";
            echo "<small>Автор: " . htmlspecialchars($row['date_match']) . " | " . htmlspecialchars($row['time_match']) . "</small>";
            echo "</div>";
            echo '</a>';
            echo "</div>";
        }
        ?>
    </div>
</div>

<div class="container">
<div class="stadium-contain">
    <div class="stadium-info">
        <h1><b>«Импульс арена»</b></h1>
        <p>Импульс арена — это современный футбольный стадион, расположенный в самом сердце Екатеринбурга. Построенный с учётом новейших стандартов UEFA, стадион вмещает до 25 000 зрителей и служит домашней ареной для местной футбольной команды.</p>
        <a class="about-btn" href="stadiumpage.php?id=6">Подробнее</a>
    </div>
    <div class="stadium-image">
        <img src="assets/img/darena.png" alt="Иконка команды Импульс">
    </div>
</div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        $('.news-slider').slick({
            infinite: false,
            slidesToShow: 3,
            slidesToScroll: 1,
            arrows: true,
            nextArrow: '<button type="button" class="slick-next">></button>',
            prevArrow: '<button type="button" class="slick-prev"><</button>',
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ],
            onAfterChange: function(slick, currentSlide) {
                const totalSlides = slick.$slides.length;
                if (currentSlide === totalSlides - 1) {
                    slick.$nextArrow.hide();
                } else {
                    slick.$nextArrow.show();
                }
                if (currentSlide === 0) {
                    slick.$prevArrow.hide();
                } else {
                    slick.$prevArrow.show();
                }
            }
        });
    });
</script>



<?php
require_once 'blade/footer.php';
?>