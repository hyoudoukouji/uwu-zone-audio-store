<?php
session_start();
require_once 'config/database.php';
require_once 'config/auth.php';

$pageTitle = 'Order History - UwU Zone';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Get all orders with their items
$query = "SELECT o.*, 
          COUNT(DISTINCT oi.id) as total_items,
          GROUP_CONCAT(DISTINCT p.name) as product_names
          FROM orders o
          LEFT JOIN order_items oi ON o.id = oi.order_id
          LEFT JOIN products p ON oi.product_id = p.id
          WHERE o.user_id = ?
          GROUP BY o.id
          ORDER BY o.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Order History</h1>

    <?php if (empty($orders)): ?>
        <div class="bg-white rounded-xl shadow-sm p-8 text-center">
            <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                <i class="fas fa-shopping-bag text-3xl text-gray-400"></i>
            </div>
            <h2 class="text-xl font-semibold mb-2">No Orders Yet</h2>
            <p class="text-gray-600 mb-6">You haven't placed any orders yet.</p>
            <a href="explore.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-shopping-cart mr-2"></i>
                Start Shopping
            </a>
        </div>
    <?php else: ?>
        <div class="space-y-6">
            <?php foreach ($orders as $order): ?>
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Order #<?php echo $order['id']; ?></p>
                                <p class="text-sm text-gray-600">
                                    Placed on <?php echo date('F j, Y', strtotime($order['created_at'])); ?>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold mb-1">Total Amount</p>
                                <p class="text-blue-600 font-bold">Rp. <?php echo number_format($order['total_amount']); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h3 class="font-medium mb-2">Order Items (<?php echo $order['total_items']; ?>)</h3>
                                <p class="text-sm text-gray-600">
                                    <?php 
                                    $products = explode(',', $order['product_names']);
                                    echo implode(', ', array_slice($products, 0, 3));
                                    if (count($products) > 3) {
                                        echo ' and ' . (count($products) - 3) . ' more';
                                    }
                                    ?>
                                </p>
                            </div>
                            <div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm
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

                        <div class="mt-6 flex flex-wrap gap-4">
                            <button onclick="viewOrderDetails(<?php echo $order['id']; ?>)"
                                    class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                <i class="fas fa-eye mr-2"></i>
                                View Details
                            </button>
                            <?php if ($order['status'] === 'completed'): ?>
                                <button onclick="reorder(<?php echo $order['id']; ?>)"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-redo mr-2"></i>
                                    Order Again
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function viewOrderDetails(orderId) {
    // You can implement a modal or redirect to a detailed view
    window.location.href = `order-details.php?id=${orderId}`;
}

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
