<?php
session_start();
require_once 'config/database.php';
require_once 'config/auth.php';

$pageTitle = 'Order Confirmation - UwU Zone';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$orderId = $_GET['id'] ?? null;

if (!$orderId) {
    header('Location: history.php');
    exit;
}

// Get order details
$query = "SELECT o.*, u.username, u.email 
          FROM orders o
          JOIN users u ON o.user_id = u.id
          WHERE o.id = ? AND o.user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$orderId, $userId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: history.php');
    exit;
}

// Get order items
$query = "SELECT oi.*, p.name, p.image_url
          FROM order_items oi
          JOIN products p ON oi.product_id = p.id
          WHERE oi.order_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$orderId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<style>
.success-check {
    width: 56px;
    height: 56px;
    background-color: #e6ffe6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
}

.success-check svg {
    width: 24px;
    height: 24px;
    color: #4ade80;
    stroke-width: 2.5;
}
</style>

<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="text-center mb-8">
        <div class="success-check">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12l5 5l9-9"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-bold mb-2">Thank You for Your Order!</h1>
        <p class="text-gray-600">Your order has been successfully placed and is being processed.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">Order Details</h2>
            <span class="text-gray-600">Order #<?php echo $orderId; ?></span>
        </div>

        <div class="space-y-4 mb-6">
            <?php foreach ($items as $item): ?>
                <div class="flex items-center gap-4">
                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                         class="w-16 h-16 object-contain rounded-lg">
                    
                    <div class="flex-1">
                        <h3 class="font-medium"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="text-sm text-gray-600">Quantity: <?php echo $item['quantity']; ?></p>
                    </div>
                    
                    <div class="text-right">
                        <p class="font-medium">Rp. <?php echo number_format($item['price']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="border-t pt-4 space-y-2">
            <div class="flex justify-between">
                <span class="text-gray-600">Subtotal</span>
                <span>Rp. <?php echo number_format($order['total_amount']); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Shipping</span>
                <span class="text-green-600">Free</span>
            </div>
            <div class="flex justify-between font-bold text-lg pt-2 border-t">
                <span>Total</span>
                <span>Rp. <?php echo number_format($order['total_amount']); ?></span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <h2 class="text-xl font-bold mb-4">What's Next?</h2>
        <div class="space-y-4">
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-envelope text-blue-600"></i>
                </div>
                <div>
                    <h3 class="font-medium">Order Confirmation Email</h3>
                    <p class="text-gray-600">You will receive an email confirmation with your order details.</p>
                </div>
            </div>
            
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-box text-blue-600"></i>
                </div>
                <div>
                    <h3 class="font-medium">Order Processing</h3>
                    <p class="text-gray-600">We will start processing your order immediately.</p>
                </div>
            </div>
            
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-shipping-fast text-blue-600"></i>
                </div>
                <div>
                    <h3 class="font-medium">Shipping Updates</h3>
                    <p class="text-gray-600">You will receive shipping updates once your order is dispatched.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-center gap-4">
        <a href="history.php" 
           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-history mr-2"></i>
            View Order History
        </a>
        <a href="explore.php" 
           class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
            <i class="fas fa-shopping-bag mr-2"></i>
            Continue Shopping
        </a>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
require_once 'partials/layout.php';
?>
