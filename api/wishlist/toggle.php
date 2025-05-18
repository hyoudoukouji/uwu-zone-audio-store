<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';
require_once '../../config/auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

try {
    if (!$auth->isLoggedIn()) {
        throw new Exception('You must be logged in to modify your wishlist');
    }

    // Get JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    $productId = $data['productId'] ?? null;
    $userId = $_SESSION['user_id'];

    if (!$productId) {
        throw new Exception('Product ID is required');
    }

    // Check if product is already in wishlist
    $stmt = $db->prepare("SELECT id FROM wishlist WHERE user_id = :user_id AND product_id = :product_id");
    $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
    $exists = $stmt->fetch();

    if ($exists) {
        // Remove from wishlist
        $stmt = $db->prepare("DELETE FROM wishlist WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
        $message = 'Product removed from wishlist';
        $status = 'removed';
    } else {
        // Add to wishlist
        $stmt = $db->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (:user_id, :product_id)");
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
        $message = 'Product added to wishlist';
        $status = 'added';
    }

    // Get updated wishlist count
    $stmt = $db->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    $wishlistCount = $stmt->fetchColumn();

    // Return success response
    echo json_encode([
        'status' => 'success',
        'action' => $status,
        'message' => $message,
        'wishlistCount' => $wishlistCount
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
