<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=footballclub', 'root', '');
    $result = $pdo->query('SELECT comand_id, name_comand, img_comand FROM Comands LIMIT 3');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo 'Team: ' . $row['name_comand'] . "\n";
        echo 'img_comand: ' . $row['img_comand'] . "\n";
        echo "---\n";
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
