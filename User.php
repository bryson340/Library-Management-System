<?php
// User.php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Login Method
    public function login($username, $password) {
        // SQL Injection Prevention: We use placeholders (?)
        $query = "SELECT user_id, username, password, role FROM " . $this->table_name . " WHERE username = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Secure Password Verification (Checks hash against password)
            if(password_verify($password, $row['password'])) {
                $this->id = $row['user_id'];
                $this->username = $row['username'];
                $this->role = $row['role'];
                return true;
            }
        }
        return false;
    }
}
?>