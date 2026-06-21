<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
    // Определяем текущую страницу
    $current_page = basename($_SERVER['PHP_SELF']);
    
    // Массив с метатегами для каждой страницы
    $meta_tags = [
        'index.php' => [
            'title' => 'ФК «Импульс» — официальный сайт футбольного клуба',
            'description' => 'Официальный сайт футбольного клуба «Импульс». Новости, расписание матчей, состав команды, билеты и история клуба.',
            'keywords' => 'футбол, футбольный клуб, Импульс, матчи, новости, билеты'
        ],
        'news.php' => [
            'title' => 'Новости ФК «Импульс» — последние события клуба',
            'description' => 'Актуальные новости футбольного клуба «Импульс». Анонсы матчей, интервью, результаты игр.',
            'keywords' => 'новости футбола, Импульс, анонсы матчей'
        ],
        'matches.php' => [
            'title' => 'Расписание матчей ФК «Импульс»',
            'description' => 'Расписание и результаты матчей футбольного клуба «Импульс». Билеты на домашние игры.',
            'keywords' => 'матчи, расписание, результаты, билеты, Импульс'
        ],
        'players.php' => [
            'title' => 'Состав команды ФК «Импульс» — игроки и тренеры',
            'description' => 'Состав футбольного клуба «Импульс». Информация о каждом игроке: амплуа, возраст, национальность.',
            'keywords' => 'игроки, состав команды, футболисты, Импульс'
        ],
        'stadiums.php' => [
            'title' => 'Стадионы ФК «Импульс» — домашняя арена',
            'description' => 'Информация о домашнем стадионе «Импульс арена» и других стадионах клуба.',
            'keywords' => 'стадион, Импульс арена, вместимость, адрес'
        ],
        'info_about.php' => [
            'title' => 'О футбольном клубе «Импульс» — история и достижения',
            'description' => 'История футбольного клуба «Импульс», достижения, награды и философия команды.',
            'keywords' => 'история клуба, достижения, награды, Импульс'
        ]
    ];
    
    // Берём метатеги для текущей страницы, если их нет — используем дефолтные
    $tags = $meta_tags[$current_page] ?? [
        'title' => 'ФК «Импульс» — футбольный клуб',
        'description' => 'Официальный сайт футбольного клуба «Импульс».',
        'keywords' => 'футбол, клуб, Импульс'
    ];
    ?>
    
    <title><?= htmlspecialchars($tags['title']) ?></title>
    <meta name="description" content="<?= htmlspecialchars($tags['description']) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($tags['keywords']) ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/bootstrap.min.css">
    <link rel="stylesheet" href="style/style.css?v=<?php echo filemtime(__DIR__ . '/../style/style.css'); ?>">
    <link rel="icon" href="assets/img/logo_impuls.png" type="image/png">
    <!-- Slick Carousel CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>

<!-- jQuery (необходим для работы слайдера) -->
<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Slick Carousel JS -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

    <title>ФК Импульс</title>
</head>
<body class="site-body">