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
        throw new Exception('You must be logged in to update cart');
    }

    $userId = $_SESSION['user_id'];
    
    // Get JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    $productId = $data['productId'] ?? null;
    $quantity = $data['quantity'] ?? null;

    if (!$productId || !$quantity) {
        throw new Exception('Product ID and quantity are required');
    }

    if ($quantity < 1) {
        throw new Exception('Quantity must be at least 1');
    }

    // Start transaction
    $db->beginTransaction();

    try {
        // Check product stock
        $query = "SELECT stock, price, name FROM products WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            throw new Exception('Product not found');
        }

        if ($quantity > $product['stock']) {
            throw new Exception("Only {$product['stock']} items available for {$product['name']}");
        }

        // Update cart quantity
        $query = "INSERT OR REPLACE INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$userId, $productId, $quantity]);

        // Get updated cart totals
        $query = "SELECT 
                    SUM(c.quantity) as total_items,
                    SUM(c.quantity * p.price) as total_amount
                 FROM cart c 
                 JOIN products p ON c.product_id = p.id 
                 WHERE c.user_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$userId]);
        $totals = $stmt->fetch(PDO::FETCH_ASSOC);

        // Commit transaction
        $db->commit();

        // Return success response with updated totals
        echo json_encode([
            'status' => 'success',
            'message' => 'Cart updated successfully',
            'cartCount' => (int)($totals['total_items'] ?? 0),
            'cartTotal' => (float)($totals['total_amount'] ?? 0),
            'itemTotal' => $quantity * $product['price']
        ]);

    } catch (Exception $e) {
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
