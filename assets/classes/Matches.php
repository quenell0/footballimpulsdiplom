<?php

class Matches
{
    private $conn;
    private $tablname = "Matches";

    // Поля таблицы
    public $id_match;
    public $title_match;
    public $img_match;
    public $date_match;
    public $time_match;
    public $description_match;
    public $home_team;
    public $away_team;
    public $score_home_team;
    public $score_away_team;
    public $price_ticket;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Получение всех записей
    public function getAll()
    {
        $query = "SELECT * FROM " . $this->tablname . " ORDER BY id_match ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Получение записи по ID
    public function readName($id)
    {
        $this->id_match = $id;
        $query = "SELECT * FROM " . $this->tablname . " WHERE id_match = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_match);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Проверка на совпадение имени
    public function proverka($name)
    {
        $query = "SELECT COUNT(*) AS kol FROM " . $this->tablname . " WHERE title_match = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$name]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['kol'];
    }

    // Создание новой записи
    public function create()
    {
        $query = "INSERT INTO " . $this->tablname . " 
        (title_match, img_match, date_match, time_match, description_match, home_team, away_team, score_home_team, score_away_team, price_ticket) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->title_match);
        $stmt->bindParam(2, $this->img_match);
        $stmt->bindParam(3, $this->date_match);
        $stmt->bindParam(4, $this->time_match);
        $stmt->bindParam(5, $this->description_match);
        $stmt->bindParam(6, $this->home_team);
        $stmt->bindParam(7, $this->away_team);
        $stmt->bindParam(8, $this->score_home_team);
        $stmt->bindParam(9, $this->score_away_team);
        $stmt->bindParam(10, $this->price_ticket);
        
        return $stmt->execute();
    }

    // Подсчёт количества игроков
    public function numberPlayers()
    {
        $query = "SELECT COUNT(*) AS kol FROM " . $this->tablname;
        $result = $this->conn->query($query)->fetch(PDO::FETCH_ASSOC);
        return $result['kol'];
    }

    // Удаление по ID
    public function delete($id)
    {
        $query = "DELETE FROM " . $this->tablname . " WHERE id_match = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    // Редактирование игрока
    public function edit($id)
    {
        $this->id_match = $id;
    
        // Если изображение не передано, используем текущее значение
        if (empty($this->img_match)) {
            // Получаем текущее значение изображения из базы данных
            $currentData = $this->readName($id);
            $this->img_match = $currentData['img_match'];
        }
    
        $query = "UPDATE " . $this->tablname . " 
            SET title_match = ?, img_match = ?, date_match = ?, time_match = ?, description_match = ?, home_team = ?, away_team = ?, score_home_team = ?, score_away_team = ?, price_ticket = ? 
            WHERE id_match = ?";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->title_match);
        $stmt->bindParam(2, $this->img_match);
        $stmt->bindParam(3, $this->date_match);
        $stmt->bindParam(4, $this->time_match);
        $stmt->bindParam(5, $this->description_match);
        $stmt->bindParam(6, $this->home_team);
        $stmt->bindParam(7, $this->away_team);
        $stmt->bindParam(8, $this->score_home_team);
        $stmt->bindParam(9, $this->score_away_team);
        $stmt->bindParam(10, $this->price_ticket);
        $stmt->bindParam(11, $this->id_match);
    
        return $stmt->execute();
    }
    
}
