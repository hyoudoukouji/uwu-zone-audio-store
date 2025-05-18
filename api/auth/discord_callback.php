<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(isset($_GET['error'])) {
    error_log('Discord OAuth error: ' . ($_GET['error_description'] ?? 'Unknown error'));
    $_SESSION['error'] = $_GET['error_description'] ?? 'Authentication failed';
    header('Location: /login.php');
    exit;
}

if(!isset($_GET['code']) || empty($_GET['code'])) {
    error_log('Discord OAuth callback missing code parameter');
    $_SESSION['error'] = 'No authorization code provided';
    header('Location: /login.php');
    exit;
}

$client_id = '1368608748087541760';
$client_secret = 'tnu1bSvipsmR2r-7SFyzfaQ_LnquEVxR';
$redirect_uri = 'https://z6yw42-8000.csb.app/api/auth/discord_callback.php';

// Exchange the code for an access token
$token_url = "https://discord.com/api/oauth2/token";
$data = [
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'grant_type' => 'authorization_code',
    'code' => $_GET['code'],
    'redirect_uri' => $redirect_uri
];

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $token_url,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($data),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded']
]);

$response = curl_exec($curl);
$err = curl_error($curl);

if($err) {
    error_log('Discord token request error: ' . $err);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to connect to Discord'
    ]);
    exit;
}

$token_data = json_decode($response, true);

if(!isset($token_data['access_token'])) {
    error_log('Discord token response missing access_token: ' . $response);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to get access token'
    ]);
    exit;
}

// Get user data from Discord
$user_url = "https://discord.com/api/users/@me";
curl_setopt_array($curl, [
    CURLOPT_URL => $user_url,
    CURLOPT_HTTPGET => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $token_data['access_token']
    ]
]);

$response = curl_exec($curl);
curl_close($curl);

$user_data = json_decode($response, true);

if(!isset($user_data['id'])) {
    error_log('Discord user data response missing id: ' . $response);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to get user data'
    ]);
    exit;
}

// Connect to database
$database = new Database();
$db = $database->getConnection();

try {
    // Check if user exists
    $stmt = $db->prepare("SELECT * FROM users WHERE discord_id = :discord_id OR email = :email");
    $stmt->execute([
        'discord_id' => $user_data['id'],
        'email' => $user_data['email']
    ]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user) {
        // Update existing user's Discord info if needed
        if(empty($user['discord_id'])) {
            $stmt = $db->prepare("UPDATE users SET discord_id = :discord_id, profile_image = :avatar WHERE id = :id");
            $avatar_url = "https://cdn.discordapp.com/avatars/{$user_data['id']}/{$user_data['avatar']}.png";
            $stmt->execute([
                'discord_id' => $user_data['id'],
                'avatar' => $avatar_url,
                'id' => $user['id']
            ]);
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['profile_image'] = $user['profile_image'];

        // Redirect to home page after successful login
        header('Location: /');
        exit;
    } else {
        // Create new user
        $username = $user_data['username'] . '#' . $user_data['discriminator'];
        $avatar_url = "https://cdn.discordapp.com/avatars/{$user_data['id']}/{$user_data['avatar']}.png";
        
        $stmt = $db->prepare("INSERT INTO users (username, email, discord_id, profile_image) VALUES (:username, :email, :discord_id, :profile_image)");
        $stmt->execute([
            'username' => $username,
            'email' => $user_data['email'],
            'discord_id' => $user_data['id'],
            'profile_image' => $avatar_url
        ]);

        $userId = $db->lastInsertId();
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['profile_image'] = $avatar_url;

        // Redirect to home page after successful registration
        header('Location: /');
        exit;
    }
} catch(Exception $e) {
    error_log('Database error: ' . $e->getMessage());
    $_SESSION['error'] = 'Database error occurred. Please try again.';
    header('Location: /login.php');
    exit;
}
?>
