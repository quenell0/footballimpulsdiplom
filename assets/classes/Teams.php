<?php

class Teams
{
    private $conn;
    private $tablname = "Comands";

    // Поля таблицы
    public $comand_id;
    public $name_comand;
    public $img_comand;
    public $icon_comand;
    public $trener_comand;
    public $discription_comand;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Получение всех записей
    public function getAll()
    {
        $query = "SELECT * FROM " . $this->tablname . " ORDER BY comand_id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Получение записи по ID
    public function readName($id)
    {
        $this->comand_id = $id;
        $query = "SELECT * FROM " . $this->tablname . " WHERE comand_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->comand_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Проверка на совпадение имени
    public function proverka($name)
    {
        $query = "SELECT COUNT(*) AS kol FROM " . $this->tablname . " WHERE name_comand = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$name]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['kol'];
    }

    // Создание новой записи
    public function create()
    {
        $query = "INSERT INTO " . $this->tablname . " 
        (name_comand, img_comand, icon_comand, trener_comand, discription_comand) 
        VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->name_comand);
        $stmt->bindParam(2, $this->img_comand);
        $stmt->bindParam(3, $this->icon_comand);
        $stmt->bindParam(4, $this->trener_comand);
        $stmt->bindParam(5, $this->discription_comand);
        
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
        $query = "DELETE FROM " . $this->tablname . " WHERE comand_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    // Редактирование игрока
    public function edit($id)
    {
        $this->comand_id = $id;
    
        // Если изображение не передано, используем текущее значение
        if (empty($this->img_comand)) {
            // Получаем текущее значение изображения из базы данных
            $currentData = $this->readName($id);
            $this->img_comand = $currentData['img_comand'];
        }
        if (empty($this->icon_comand)) {
            // Получаем текущее значение изображения из базы данных
            $currentIcon = $this->readName($id);
            $this->icon_comand = $currentIcon['icon_comand'];
        }
    
        $query = "UPDATE " . $this->tablname . " 
            SET name_comand = ?, img_comand = ?, icon_comand = ?, trener_comand = ?, discription_comand = ? 
            WHERE comand_id = ?";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->name_comand);
        $stmt->bindParam(2, $this->img_comand);
        $stmt->bindParam(3, $this->icon_comand);
        $stmt->bindParam(4, $this->trener_comand);
        $stmt->bindParam(5, $this->discription_comand);
        $stmt->bindParam(6, $this->comand_id);
    
        return $stmt->execute();
    }
    
}
