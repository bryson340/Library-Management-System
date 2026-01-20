<?php
// Database.php
class Database {
    private $host = "localhost";
    private $db_name = "library_db";
    private $username = "root";
    private $password = ""; // Leave empty for XAMPP default
    public $conn;

    // Method to get the database connection
    public function getConnection() {
        $this->conn = null;

        try {
            // Using PDO is safer and prevents SQL Injection (Resume Point)
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>