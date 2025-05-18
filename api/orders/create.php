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
        throw new Exception('You must be logged in to place an order');
    }

    $userId = $_SESSION['user_id'];
    
    // Start transaction
    $db->beginTransaction();

    try {
        // Get cart items with current stock levels
        $query = "SELECT c.*, p.stock, p.price, p.name 
                  FROM cart c 
                  JOIN products p ON c.product_id = p.id 
                  WHERE c.user_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$userId]);
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($cartItems)) {
            throw new Exception('Your cart is empty');
        }

        // Calculate total amount and validate stock
        $totalAmount = 0;
        $stockErrors = [];

        foreach ($cartItems as $item) {
            // Get current stock level
            $stockQuery = "SELECT stock FROM products WHERE id = ?";
            $stockStmt = $db->prepare($stockQuery);
            $stockStmt->execute([$item['product_id']]);
            $currentStock = $stockStmt->fetchColumn();

            if ($item['quantity'] > $currentStock) {
                $stockErrors[] = "Not enough stock for {$item['name']}. Available: {$currentStock}";
            }
            $totalAmount += $item['price'] * $item['quantity'];
        }

        if (!empty($stockErrors)) {
            throw new Exception(implode("\n", $stockErrors));
        }

        // Create order
        $query = "INSERT INTO orders (user_id, total_amount, status, created_at) VALUES (?, ?, 'pending', CURRENT_TIMESTAMP)";
        $stmt = $db->prepare($query);
        $stmt->execute([$userId, $totalAmount]);
        $orderId = $db->lastInsertId();

        // Process each item
        foreach ($cartItems as $item) {
            // Update stock with check
            $updateStock = $db->prepare("UPDATE products 
                                       SET stock = stock - ? 
                                       WHERE id = ? AND stock >= ?");
            $result = $updateStock->execute([
                $item['quantity'],
                $item['product_id'],
                $item['quantity']
            ]);

            // Verify stock update
            if ($updateStock->rowCount() === 0) {
                throw new Exception("Stock update failed for {$item['name']}. Please try again.");
            }

            // Add order item
            $insertOrderItem = $db->prepare("INSERT INTO order_items 
                                           (order_id, product_id, quantity, price, created_at) 
                                           VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)");
            $insertOrderItem->execute([
                $orderId,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            ]);

            // Update sales count
            $updateSales = $db->prepare("UPDATE products 
                                       SET sales_count = sales_count + ? 
                                       WHERE id = ?");
            $updateSales->execute([
                $item['quantity'],
                $item['product_id']
            ]);
        }

        // Clear user's cart
        $query = "DELETE FROM cart WHERE user_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$userId]);

        // Update order status
        $query = "UPDATE orders SET status = 'completed' WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$orderId]);

        // Commit transaction
        $db->commit();

        // Return success response
        echo json_encode([
            'status' => 'success',
            'message' => 'Order placed successfully',
            'orderId' => $orderId,
            'totalAmount' => $totalAmount
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
