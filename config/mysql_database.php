<?php
class MySQLDatabase {
    private $host = 'localhost';
    private $db_name = 'uwu_zone';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            // Fallback to SQLite if MySQL connection fails
            try {
                $this->conn = new PDO("sqlite:" . __DIR__ . "/../database/uwu_zone.db");
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $ex) {
                throw new Exception("Database connection failed: " . $ex->getMessage());
            }
        }

        return $this->conn;
    }
}
?>
