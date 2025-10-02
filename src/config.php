<?php
class Database {
    private $host = "localhost";
    private $db_name = "login_systeem";
    private $username = "root";  // Pas aan naar jouw situatie
    private $password = "";      // Pas aan naar jouw situatie
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                                  $this->username, 
                                  $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>