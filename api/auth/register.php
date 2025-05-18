<?php
session_start();
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

    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $username = trim($data['username'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $confirm_password = $data['confirm_password'] ?? '';

    // Validation checks
    if (empty($username)) {
        throw new Exception('Username is required');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }

    if (strlen($password) < 6) {
        throw new Exception('Password must be at least 6 characters');
    }

    if ($password !== $confirm_password) {
        throw new Exception('Passwords do not match');
    }

    // Check username length and format
    if (strlen($username) < 3 || strlen($username) > 30) {
        throw new Exception('Username must be between 3 and 30 characters');
    }

    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        throw new Exception('Username can only contain letters, numbers, and underscores');
    }

    // Check if username already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        throw new Exception('Username is already taken');
    }

    // Check if email already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        throw new Exception('Email is already registered');
    }

    // Register the user
    if ($auth->register($username, $email, $password)) {
        // Log the user in automatically after registration
        if ($auth->login($email, $password)) {
            $user = $auth->getCurrentUser();
            
            // Regenerate session ID for security
            session_regenerate_id(true);

            echo json_encode([
                'status' => 'success',
                'message' => 'Registration successful! You are now logged in.',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'profile_image' => $user['profile_image']
                ],
                'cartCount' => 0,
                'wishlistCount' => 0
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'message' => 'Registration successful! Please log in.'
            ]);
        }
    } else {
        throw new Exception('Registration failed. Please try again.');
    }

} catch (Exception $e) {
    error_log('Registration error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
