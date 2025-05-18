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
        throw new Exception('You must be logged in to reorder items');
    }

    $userId = $_SESSION['user_id'];
    
    // Get JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    $orderId = $data['orderId'] ?? null;

    if (!$orderId) {
        throw new Exception('Order ID is required');
    }

    // Verify order belongs to user
    $query = "SELECT id FROM orders WHERE id = ? AND user_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$orderId, $userId]);
    if (!$stmt->fetch()) {
        throw new Exception('Order not found');
    }

    // Start transaction
    $db->beginTransaction();

    try {
        // Get order items
        $query = "SELECT product_id, quantity FROM order_items WHERE order_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Check stock availability
        foreach ($items as $item) {
            $query = "SELECT stock FROM products WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$item['product_id']]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product || $product['stock'] < $item['quantity']) {
                throw new Exception('Some items are out of stock');
            }
        }

        // Clear existing cart
        $query = "DELETE FROM cart WHERE user_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$userId]);

        // Add items to cart
        $query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $db->prepare($query);

        foreach ($items as $item) {
            $stmt->execute([
                $userId,
                $item['product_id'],
                $item['quantity']
            ]);
        }

        // Get updated cart count
        $query = "SELECT SUM(quantity) as count FROM cart WHERE user_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$userId]);
        $cartCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

        // Commit transaction
        $db->commit();

        // Return success response
        echo json_encode([
            'status' => 'success',
            'message' => 'Items added to cart successfully',
            'cartCount' => (int)$cartCount
        ]);

    } catch (Exception $e) {
        // Rollback transaction on error
        $db->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
