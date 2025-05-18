<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';
require_once '../../config/google.php';
require_once '../../vendor/autoload.php';

use Google\Client as Google_Client;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $credential = $data['credential'] ?? '';
        $isRegister = $data['isRegister'] ?? false;

        if (empty($credential)) {
            throw new Exception('Google credential is required');
        }

        // Initialize and configure Google Client
        $client = new Google_Client([
            'client_id' => GOOGLE_CLIENT_ID,
            'verify_token_expiration' => true
        ]);

        // Verify the Google ID token
        $payload = $client->verifyIdToken($credential);
        if (!$payload) {
            throw new Exception('Invalid token payload');
        }

        // Extract user information from payload
        $googleId = $payload['sub'];
        $email = $payload['email'];
        $name = $payload['name'];
        $picture = $payload['picture'];

        // Check if user exists
        $stmt = $db->prepare("SELECT * FROM users WHERE google_id = :google_id OR email = :email");
        $stmt->execute(['google_id' => $googleId, 'email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // User exists, update their Google ID if necessary
            if (empty($user['google_id'])) {
                $stmt = $db->prepare("UPDATE users SET google_id = :google_id, profile_image = :profile_image WHERE id = :id");
                $stmt->execute([
                    'google_id' => $googleId,
                    'profile_image' => $picture,
                    'id' => $user['id']
                ]);
            }

            // Log them in
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['profile_image'] = $user['profile_image'];
            $_SESSION['google_token'] = $credential; // Store Google token in session

            $response['success'] = true;
            $response['message'] = 'Login successful';
            $response['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'profile_image' => $user['profile_image']
            ];
        } else {
            if (!$isRegister) {
                $response['message'] = 'No account found with this Google account. Please register first.';
            } else {
                // Create new user
                $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name)) . rand(100, 999);
                
                $stmt = $db->prepare("INSERT INTO users (username, email, google_id, profile_image) VALUES (:username, :email, :google_id, :profile_image)");
                $stmt->execute([
                    'username' => $username,
                    'email' => $email,
                    'google_id' => $googleId,
                    'profile_image' => $picture
                ]);

                $userId = $db->lastInsertId();
                $_SESSION['user_id'] = $userId;
                $_SESSION['username'] = $username;
                $_SESSION['profile_image'] = $picture;
                $_SESSION['google_token'] = $credential; // Store Google token in session

                $response['success'] = true;
                $response['message'] = 'Registration successful';
                $response['user'] = [
                    'id' => $userId,
                    'username' => $username,
                    'email' => $email,
                    'profile_image' => $picture
                ];
            }
        }
    } catch (Exception $e) {
        error_log('Google auth error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        $response['success'] = false;
        $response['message'] = 'Authentication failed: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>
