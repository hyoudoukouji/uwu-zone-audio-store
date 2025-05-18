<?php
session_start();
require_once 'config/database.php';
require_once 'config/auth.php';

$pageTitle = 'Shopping Cart - UwU Zone';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Get cart items with product details including stock
$query = "SELECT c.*, p.name, p.price, p.image_url, p.stock, p.id as product_id 
          FROM cart c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = ?
          GROUP BY p.id
          ORDER BY c.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total and check stock
$total = 0;
$hasStockIssue = false;
foreach ($cartItems as &$item) {
    $item['subtotal'] = $item['price'] * $item['quantity'];
    $total += $item['subtotal'];
    if ($item['quantity'] > $item['stock']) {
        $hasStockIssue = true;
        $item['stockError'] = true;
    }
}

ob_start();
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-8">Shopping Cart</h1>

    <?php if (empty($cartItems)): ?>
        <div class="bg-white rounded-xl shadow-sm p-8 text-center">
            <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                <i class="fas fa-shopping-cart text-3xl text-gray-400"></i>
            </div>
            <h2 class="text-xl font-semibold mb-2">Your cart is empty</h2>
            <p class="text-gray-600 mb-6">Add some products to your cart and start shopping!</p>
            <a href="explore.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-shopping-bag mr-2"></i>
                Continue Shopping
            </a>
        </div>
    <?php else: ?>
        <?php if ($hasStockIssue): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            Some items in your cart exceed available stock. 
                            Please update the quantities before proceeding to checkout.
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Cart Items -->
            <div class="lg:w-2/3">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="divide-y">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="p-6 flex flex-wrap md:flex-nowrap items-center gap-4 <?php echo isset($item['stockError']) ? 'bg-red-50' : ''; ?>">
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                     class="w-24 h-24 object-contain rounded-lg">
                                
                                <div class="flex-1">
                                    <h3 class="font-semibold mb-2"><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p class="text-blue-600 font-bold mb-2">Rp. <?php echo number_format($item['price']); ?></p>
                                    <p class="text-sm text-gray-600">Available Stock: <?php echo $item['stock']; ?></p>
                                    <?php if (isset($item['stockError'])): ?>
                                        <p class="text-red-600 text-sm mt-1">
                                            Quantity exceeds available stock
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <button onclick="updateQuantity(<?php echo $item['product_id']; ?>, 'decrease')"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border hover:bg-gray-100 transition-colors"
                                            <?php echo $item['quantity'] <= 1 ? 'disabled' : ''; ?>>
                                        <i class="fas fa-minus text-gray-600"></i>
                                    </button>
                                    
                                    <input type="number" 
                                           id="quantity-<?php echo $item['product_id']; ?>"
                                           value="<?php echo $item['quantity']; ?>"
                                           min="1"
                                           max="<?php echo $item['stock']; ?>"
                                           class="w-16 text-center border rounded-lg py-2 <?php echo isset($item['stockError']) ? 'border-red-300' : ''; ?>"
                                           onchange="updateQuantity(<?php echo $item['product_id']; ?>, 'set', this.value)">
                                    
                                    <button onclick="updateQuantity(<?php echo $item['product_id']; ?>, 'increase')"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border hover:bg-gray-100 transition-colors"
                                            <?php echo $item['quantity'] >= $item['stock'] ? 'disabled' : ''; ?>>
                                        <i class="fas fa-plus text-gray-600"></i>
                                    </button>
                                </div>

                                <div class="text-right">
                                    <p class="font-bold mb-2">Rp. <?php echo number_format($item['subtotal']); ?></p>
                                    <button onclick="removeFromCart(<?php echo $item['product_id']; ?>)"
                                            class="text-red-600 hover:text-red-700 text-sm">
                                        <i class="fas fa-trash mr-1"></i>
                                        Remove
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-4">
                    <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span>Rp. <?php echo number_format($total); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping</span>
                            <span class="text-green-600">Free</span>
                        </div>
                        <div class="border-t pt-4">
                            <div class="flex justify-between font-bold">
                                <span>Total</span>
                                <span>Rp. <?php echo number_format($total); ?></span>
                            </div>
                        </div>
                    </div>

                    <?php if ($hasStockIssue): ?>
                        <button disabled
                                class="block w-full py-3 px-4 bg-gray-400 text-white text-center rounded-lg cursor-not-allowed">
                            Update Cart to Continue
                        </button>
                    <?php else: ?>
                        <a href="checkout.php" 
                           class="block w-full py-3 px-4 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition-colors">
                            Proceed to Checkout
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
let isUpdating = false;

function updateQuantity(productId, action, value = null) {
    if (isUpdating) return;
    
    let quantity;
    const input = document.getElementById(`quantity-${productId}`);
    const currentQty = parseInt(input.value);
    const maxStock = parseInt(input.max);

    switch(action) {
        case 'increase':
            quantity = currentQty + 1;
            if (quantity > maxStock) {
                showNotification('Cannot exceed available stock', 'error');
                return;
            }
            break;
        case 'decrease':
            quantity = currentQty - 1;
            if (quantity < 1) {
                showNotification('Quantity cannot be less than 1', 'error');
                return;
            }
            break;
        case 'set':
            quantity = parseInt(value);
            if (quantity < 1) {
                quantity = 1;
                showNotification('Quantity cannot be less than 1', 'error');
            }
            if (quantity > maxStock) {
                quantity = maxStock;
                showNotification('Cannot exceed available stock', 'error');
            }
            break;
        default:
            return;
    }

    isUpdating = true;

    fetch('api/cart/update.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            productId: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        } else {
            showNotification(data.message || 'Failed to update quantity', 'error');
            isUpdating = false;
        }
    })
    .catch(error => {
        showNotification('Failed to update quantity', 'error');
        isUpdating = false;
    });
}

function removeFromCart(productId) {
    if (!confirm('Are you sure you want to remove this item?')) return;

    fetch('api/cart/remove.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            productId: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        } else {
            showNotification(data.message || 'Failed to remove item', 'error');
        }
    })
    .catch(error => {
        showNotification('Failed to remove item', 'error');
    });
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white transform transition-all duration-300 z-50`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>

<?php
$pageContent = ob_get_clean();
require_once 'partials/layout.php';
?>
