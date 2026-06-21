<?php

class Player
{
    private $conn;
    private $tablname = "Players";

    // Поля таблицы
    public $player_id;
    public $name_player;
    public $img_player;
    public $position;
    public $age_player;
    public $nationality_player;
    public $description_player;
    public $comand_id;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Получение всех записей
    public function getAll()
    {
        $query = "SELECT Players.*, Position.name_position, Comands.name_comand AS team_name
            FROM Players
            LEFT JOIN Position ON Players.position = Position.position_id
            LEFT JOIN Comands ON Players.comand_id = Comands.comand_id
            ORDER BY Players.player_id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Получение записи по ID
    public function readName($id)
    {
        $this->player_id = $id;
        $query = "SELECT * FROM " . $this->tablname . " WHERE player_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->player_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Проверка на совпадение имени
    public function proverka($name)
    {
        $query = "SELECT COUNT(*) AS kol FROM " . $this->tablname . " WHERE name_player = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$name]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['kol'];
    }

    // Создание новой записи
    public function create()
    {
        $query = "INSERT INTO " . $this->tablname . " 
        (name_player, img_player, position, age_player, nationality_player, description_player, comand_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->name_player);
        $stmt->bindParam(2, $this->img_player);
        $stmt->bindParam(3, $this->position);
        $stmt->bindParam(4, $this->age_player);
        $stmt->bindParam(5, $this->nationality_player);
        $stmt->bindParam(6, $this->description_player);
        $stmt->bindValue(7, $this->comand_id ?: null, $this->comand_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
        
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
        $query = "DELETE FROM " . $this->tablname . " WHERE player_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    // Редактирование игрока
    public function edit($id)
    {
        $this->player_id = $id;
    
        if (empty($this->img_player)) {
            $currentData = $this->readName($id);
            $this->img_player = $currentData['img_player'];
        }
    
        $query = "UPDATE " . $this->tablname . " 
            SET name_player = ?, img_player = ?, position = ?, age_player = ?, nationality_player = ?, description_player = ?, comand_id = ? 
            WHERE player_id = ?";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->name_player);
        $stmt->bindParam(2, $this->img_player);
        $stmt->bindParam(3, $this->position);
        $stmt->bindParam(4, $this->age_player);
        $stmt->bindParam(5, $this->nationality_player);
        $stmt->bindParam(6, $this->description_player);
        $stmt->bindValue(7, $this->comand_id ?: null, $this->comand_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindParam(8, $this->player_id);
    
        return $stmt->execute();
    }
    
}
