<?php
class Auth {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function login($email, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['profile_image'] = $user['profile_image'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function register($username, $email, $password) {
        try {
            // Check if email already exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                // Email already exists
                return false;
            }
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            return $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashed_password
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function logout() {
        session_destroy();
        return true;
    }
    
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        try {
            $stmt = $this->db->prepare("SELECT id, username, email, profile_image, address, full_name, phone_number FROM users WHERE id = :id");
            $stmt->execute(['id' => $_SESSION['user_id']]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
    
    public function updateProfile($userId, $data) {
        try {
            $updates = [];
            $params = ['id' => $userId];
            
            if (isset($data['username'])) {
                $updates[] = "username = :username";
                $params['username'] = $data['username'];
            }
            
            if (isset($data['email'])) {
                $updates[] = "email = :email";
                $params['email'] = $data['email'];
            }
            
            if (isset($data['profile_image'])) {
                $updates[] = "profile_image = :profile_image";
                $params['profile_image'] = $data['profile_image'];
            }

            if (isset($data['address'])) {
                $updates[] = "address = :address";
                $params['address'] = $data['address'];
            }

            if (isset($data['full_name'])) {
                $updates[] = "full_name = :full_name";
                $params['full_name'] = $data['full_name'];
            }

            if (isset($data['phone_number'])) {
                $updates[] = "phone_number = :phone_number";
                $params['phone_number'] = $data['phone_number'];
            }
            
            if (empty($updates)) {
                return false;
            }
            
            $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
