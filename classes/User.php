<?php
/**
 * User Model Class
 * Handles user authentication and CRUD operations
 */
class User {
    private $conn;
    private $table_name = "users";

    // User properties
    public $id;
    public $username;
    public $email;
    public $password;
    public $password_hash;
    public $created_at;
    public $updated_at;

    /**
     * Constructor
     * @param PDO $db Database connection
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create new user account
     * @return bool True on success, false on failure
     */
    public function create() {
        try {
            // Check if username or email already exists
            if ($this->usernameExists()) {
                return false;
            }
            if ($this->emailExists()) {
                return false;
            }

            $query = "INSERT INTO " . $this->table_name . " 
                     (username, email, password_hash) 
                     VALUES (:username, :email, :password_hash)";

            $stmt = $this->conn->prepare($query);

            // Sanitize inputs
            $this->username = htmlspecialchars(strip_tags($this->username));
            $this->email = htmlspecialchars(strip_tags($this->email));
            
            // Hash password using bcrypt
            $this->password_hash = password_hash($this->password, PASSWORD_BCRYPT);

            // Bind parameters
            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':password_hash', $this->password_hash);

            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }

            return false;

        } catch (PDOException $e) {
            error_log("User Create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Authenticate user login
     * @return bool|array User data on success, false on failure
     */
    public function login() {
        try {
            $query = "SELECT id, username, email, password_hash, created_at 
                     FROM " . $this->table_name . " 
                     WHERE username = :username 
                     LIMIT 1";

            $stmt = $this->conn->prepare($query);
            
            // Sanitize username
            $this->username = htmlspecialchars(strip_tags($this->username));
            $stmt->bindParam(':username', $this->username);
            
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch();
                
                // Verify password
                if (password_verify($this->password, $row['password_hash'])) {
                    $this->id = $row['id'];
                    $this->email = $row['email'];
                    $this->created_at = $row['created_at'];
                    
                    return [
                        'id' => $row['id'],
                        'username' => $row['username'],
                        'email' => $row['email'],
                        'created_at' => $row['created_at']
                    ];
                }
            }

            return false;

        } catch (PDOException $e) {
            error_log("User Login Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if username already exists
     * @return bool True if exists, false otherwise
     */
    public function usernameExists() {
        try {
            $query = "SELECT id FROM " . $this->table_name . " 
                     WHERE username = :username 
                     LIMIT 1";

            $stmt = $this->conn->prepare($query);
            
            $this->username = htmlspecialchars(strip_tags($this->username));
            $stmt->bindParam(':username', $this->username);
            
            $stmt->execute();

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            error_log("Username Check Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if email already exists
     * @return bool True if exists, false otherwise
     */
    public function emailExists() {
        try {
            $query = "SELECT id FROM " . $this->table_name . " 
                     WHERE email = :email 
                     LIMIT 1";

            $stmt = $this->conn->prepare($query);
            
            $this->email = htmlspecialchars(strip_tags($this->email));
            $stmt->bindParam(':email', $this->email);
            
            $stmt->execute();

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            error_log("Email Check Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user by ID
     * @return bool|array User data on success, false on failure
     */
    public function getById() {
        try {
            $query = "SELECT id, username, email, created_at 
                     FROM " . $this->table_name . " 
                     WHERE id = :id 
                     LIMIT 1";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch();
            }

            return false;

        } catch (PDOException $e) {
            error_log("Get User Error: " . $e->getMessage());
            return false;
        }
    }
}
?>
