<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../config/database.php';
require_once '../../config/auth.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $rawInput = file_get_contents('php://input');
    error_log("Raw input: " . $rawInput);

    $data = json_decode($rawInput, true);
    
    if (!isset($data['email']) || !isset($data['password'])) {
        throw new Exception('Email and password are required');
    }

    $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
    $password = $data['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    if (strlen($password) < 6) {
        throw new Exception('Password must be at least 6 characters long');
    }

    if ($auth->login($email, $password)) {
        // Get user data
        $user = $auth->getCurrentUser();
        
        // Regenerate session ID for security
        session_regenerate_id(true);

        // Get cart count from database
        $stmt = $db->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $cartCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

        // Get wishlist count from database
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $wishlistCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

        echo json_encode([
            'status' => 'success',
            'message' => 'Login successful!',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'profile_image' => $user['profile_image']
            ],
            'cartCount' => (int)$cartCount,
            'wishlistCount' => (int)$wishlistCount
        ]);
    } else {
        throw new Exception('Invalid email or password');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
