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
        throw new Exception('You must be logged in to remove items from the cart');
    }

    // Get JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    $productId = $data['productId'] ?? null;
    $userId = $_SESSION['user_id'];

    if (!$productId) {
        throw new Exception('Product ID is required');
    }

    // Remove item from cart
    $query = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$userId, $productId]);

    // Get updated cart count
    $query = "SELECT SUM(quantity) as count FROM cart WHERE user_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $cartCount = $result['count'] ?? 0;

    // Return success response
    echo json_encode([
        'status' => 'success',
        'message' => 'Product removed from cart',
        'cartCount' => $cartCount
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
