<?php

class News
{
    private $conn;
    private $tablname = "News";

    // Поля таблицы
    public $news_id;
    public $title_news;
    public $img_news;
    public $date_published;
    public $author_news;
    public $content_news;
    public $event_id;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Получение всех записей
    public function getAll()
    {
        $query = "SELECT * FROM " . $this->tablname . " ORDER BY news_id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Получение записи по ID
    public function readName($id)
    {
        $this->news_id = $id;
        $query = "SELECT * FROM " . $this->tablname . " WHERE news_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->news_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Проверка на совпадение имени
    public function proverka($name)
    {
        $query = "SELECT COUNT(*) AS kol FROM " . $this->tablname . " WHERE title_news = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$name]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['kol'];
    }

    // Создание новой записи
    public function create()
    {
        $query = "INSERT INTO " . $this->tablname . " 
        (title_news, img_news, date_published, author_news, content_news) 
        VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->title_news);
        $stmt->bindParam(2, $this->img_news);
        $stmt->bindParam(3, $this->date_published);
        $stmt->bindParam(4, $this->author_news);
        $stmt->bindParam(5, $this->content_news);
        
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
        $query = "DELETE FROM " . $this->tablname . " WHERE news_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    // Редактирование игрока
    public function edit($id)
    {
        $this->news_id = $id;
    
        if (empty($this->img_news)) {
            $currentData = $this->readName($id);
            $this->img_news = $currentData['img_news'];
        }
    
        $query = "UPDATE " . $this->tablname . " 
            SET title_news = ?, img_news = ?, date_published = ?, author_news = ?, content_news = ?
            WHERE news_id = ?";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->title_news);
        $stmt->bindParam(2, $this->img_news);
        $stmt->bindParam(3, $this->date_published);
        $stmt->bindParam(4, $this->author_news);
        $stmt->bindParam(5, $this->content_news);
        $stmt->bindParam(6, $this->news_id);
    
        return $stmt->execute();
    }
    
}
