<?php  

class Dbh  
{  
    private $host = "localhost";  
    private $port = "3306";  
    private $dbname = "quenell_football";  // измените на название вашей БД на хостинге
    private $user = "root";  
    private $pass = "";  
    private $charset = "utf8";  

    private $options = [  
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  
        PDO::ATTR_EMULATE_PREPARES => false,  
    ];  

    private $PDO = null; 

    public function connect_pdo()  
    {  
        try {  
            $this->PDO = new PDO(  
                "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}",  
                $this->user,  
                $this->pass,  
                $this->options  
            );  
            return $this->PDO;  
        } catch (PDOException $e) {  
            error_log("Ошибка соединения с базой данных: " . $e->getMessage());  
            throw new Exception("Не удалось установить соединение с базой данных.");  
        }  
    }  

    public function close_connect()  
    {  
        $this->PDO = null;  
    }  

    public function getInfPDO()  
    {  
        return $this->PDO;  
    }  
}  
?>
