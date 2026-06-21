<?php

class Stadium
{
    private $conn;
    private $tablname = "Stadium";

    // Поля таблицы
    public $stadium_id;
    public $name_stadium;
    public $img_stadium;
    public $Country;
    public $location_stadium;
    public $description_stadium;
    public $capacity_stadium;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Получение всех записей
    public function getAll()
    {
        $query = "SELECT * FROM " . $this->tablname . " ORDER BY stadium_id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Получение записи по ID
    public function readName($id)
    {
        $this->stadium_id = $id;
        $query = "SELECT * FROM " . $this->tablname . " WHERE stadium_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->stadium_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Проверка на совпадение имени
    public function proverka($name)
    {
        $query = "SELECT COUNT(*) AS kol FROM " . $this->tablname . " WHERE name_stadium = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$name]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['kol'];
    }

    // Создание новой записи
    public function create()
    {
        $query = "INSERT INTO " . $this->tablname . " 
        (name_stadium, img_stadium, Country, location_stadium, description_stadium, capacity_stadium) 
        VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->name_stadium);
        $stmt->bindParam(2, $this->img_stadium);
        $stmt->bindParam(3, $this->Country);
        $stmt->bindParam(4, $this->location_stadium);
        $stmt->bindParam(5, $this->description_stadium);
        $stmt->bindParam(6, $this->capacity_stadium);
        
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
        $query = "DELETE FROM " . $this->tablname . " WHERE stadium_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    // Редактирование игрока
    public function edit($id)
    {
        $this->stadium_id = $id;
    
        // Если изображение не передано, используем текущее значение
        if (empty($this->img_stadium)) {
            // Получаем текущее значение изображения из базы данных
            $currentData = $this->readName($id);
            $this->img_stadium = $currentData['img_stadium'];
        }
    
        $query = "UPDATE " . $this->tablname . " 
            SET name_stadium = ?, img_stadium = ?, Country = ?, location_stadium = ?, description_stadium = ?, capacity_stadium = ? 
            WHERE stadium_id = ?";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->name_stadium);
        $stmt->bindParam(2, $this->img_stadium);
        $stmt->bindParam(3, $this->Country);
        $stmt->bindParam(4, $this->location_stadium);
        $stmt->bindParam(5, $this->description_stadium);
        $stmt->bindParam(6, $this->capacity_stadium);
        $stmt->bindParam(7, $this->stadium_id);
    
        return $stmt->execute();
    }
    
}
