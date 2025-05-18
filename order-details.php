<?php
session_start();
require_once 'config/database.php';
require_once 'config/auth.php';

$pageTitle = 'Order Details - UwU Zone';

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
$query = "SELECT oi.*, p.name, p.image_url, p.description
          FROM order_items oi
          JOIN products p ON oi.product_id = p.id
          WHERE oi.order_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$orderId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="history.php" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Order History
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-8">
        <div class="p-6 border-b">
            <div class="flex flex-wrap justify-between items-start gap-4">
                <div>
                    <h1 class="text-2xl font-bold mb-2">Order #<?php echo $order['id']; ?></h1>
                    <p class="text-gray-600">Placed on <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></p>
                </div>
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                    <?php echo match($order['status']) {
                        'completed' => 'bg-green-100 text-green-800',
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                        default => 'bg-gray-100 text-gray-800'
                    }; ?>">
                    <i class="fas fa-<?php echo match($order['status']) {
                        'completed' => 'check',
                        'pending' => 'clock',
                        'cancelled' => 'times',
                        default => 'circle'
                    }; ?> mr-2"></i>
                    <?php echo ucfirst($order['status']); ?>
                </span>
            </div>
        </div>

        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Order Information -->
                <div>
                    <h2 class="text-lg font-semibold mb-4">Order Information</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Customer Name</p>
                            <p class="font-medium"><?php echo htmlspecialchars($order['username']); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email Address</p>
                            <p class="font-medium"><?php echo htmlspecialchars($order['email']); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Order Status</p>
                            <p class="font-medium"><?php echo ucfirst($order['status']); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div>
                    <h2 class="text-lg font-semibold mb-4">Order Summary</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span>Rp. <?php echo number_format($order['total_amount']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping</span>
                            <span class="text-green-600">Free</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t font-bold">
                            <span>Total</span>
                            <span>Rp. <?php echo number_format($order['total_amount']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Order Items</h2>
        </div>

        <div class="divide-y">
            <?php foreach ($items as $item): ?>
                <div class="p-6 flex items-center space-x-4">
                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                         class="w-24 h-24 object-contain rounded-lg">
                    
                    <div class="flex-1">
                        <h3 class="font-semibold mb-1"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="text-sm text-gray-600 mb-2"><?php echo htmlspecialchars($item['description']); ?></p>
                        <p class="text-sm">
                            <span class="text-gray-600">Quantity:</span> 
                            <span class="font-medium"><?php echo $item['quantity']; ?></span>
                        </p>
                    </div>
                    
                    <div class="text-right">
                        <p class="font-semibold">Rp. <?php echo number_format($item['price']); ?></p>
                        <p class="text-sm text-gray-600">per item</p>
                        <p class="font-bold text-blue-600 mt-1">
                            Rp. <?php echo number_format($item['price'] * $item['quantity']); ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($order['status'] === 'completed'): ?>
        <div class="mt-8 text-center">
            <button onclick="reorder(<?php echo $order['id']; ?>)"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-redo mr-2"></i>
                Order These Items Again
            </button>
        </div>
    <?php endif; ?>
</div>

<script>
function reorder(orderId) {
    fetch(`api/orders/reorder.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ orderId: orderId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showNotification('Items added to cart!', 'success');
            setTimeout(() => {
                window.location.href = 'cart.php';
            }, 1500);
        } else {
            throw new Error(data.message || 'Failed to reorder');
        }
    })
    .catch(error => {
        showNotification(error.message, 'error');
    });
}
</script>

<?php
$pageContent = ob_get_clean();
require_once 'partials/layout.php';
?>
