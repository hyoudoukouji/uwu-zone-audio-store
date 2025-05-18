<?php
header('Content-Type: application/json');

require_once '../../config/database.php';
require_once '../../config/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

try {
    if (!$auth->isLoggedIn()) {
        throw new Exception('You must be logged in to add items to the cart');
    }

    // Get JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    $productId = $data['productId'] ?? null;
    $quantity = intval($data['quantity'] ?? 1);
    $userId = $_SESSION['user_id'];

    if (!$productId) {
        throw new Exception('Product ID is required');
    }

    if ($quantity < 1) {
        throw new Exception('Quantity must be at least 1');
    }

    // Start transaction
    $db->beginTransaction();

    try {
        // Get product stock from database
        $query = "SELECT stock, price, name FROM products WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            throw new Exception('Product not found');
        }

        // Check if product already exists in cart
        $query = "SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$userId, $productId]);
        $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);

        $newQuantity = $quantity;
        if ($existingItem) {
            $newQuantity = $existingItem['quantity'] + $quantity;
        }

        // Validate stock
        if ($newQuantity > $product['stock']) {
            throw new Exception("Only {$product['stock']} items available for {$product['name']}");
        }

        if ($existingItem) {
            // Update existing cart item
            $query = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$newQuantity, $userId, $productId]);
        } else {
            // Add new cart item
            $query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->execute([$userId, $productId, $newQuantity]);
        }

        // Get updated cart totals
        $query = "SELECT 
                    COUNT(DISTINCT product_id) as unique_items,
                    SUM(quantity) as total_items,
                    SUM(c.quantity * p.price) as total_amount
                 FROM cart c 
                 JOIN products p ON c.product_id = p.id 
                 WHERE c.user_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$userId]);
        $totals = $stmt->fetch(PDO::FETCH_ASSOC);

        // Commit transaction
        $db->commit();

        // Return success response
        echo json_encode([
            'status' => 'success',
            'message' => 'Product added to cart',
            'cartCount' => (int)($totals['total_items'] ?? 0),
            'uniqueItems' => (int)($totals['unique_items'] ?? 0),
            'totalAmount' => (float)($totals['total_amount'] ?? 0)
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
