<?php
session_start();
require_once 'config/database.php';
require_once 'config/auth.php';

$pageTitle = 'Checkout - UwU Zone';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Get cart items with product details including stock
$query = "SELECT c.*, p.name, p.price, p.image_url, p.stock 
          FROM cart c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$userId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total and check stock
$total = 0;
$hasStockIssue = false;
foreach ($items as $item) {
    if ($item['quantity'] > $item['stock']) {
        $hasStockIssue = true;
    }
    $total += $item['price'] * $item['quantity'];
}

ob_start();
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Left Column - Order Summary -->
        <div class="lg:w-2/3">
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-2xl font-bold mb-4">Order Summary</h2>
                
                <?php if (empty($items)): ?>
                    <div class="text-center py-8">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-600 mb-4">Your cart is empty</p>
                        <a href="explore.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Continue Shopping
                        </a>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($items as $item): ?>
                            <div class="flex items-center p-4 border rounded-lg">
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                     class="w-20 h-20 object-contain rounded-lg">
                                
                                <div class="ml-4 flex-1">
                                    <h3 class="font-semibold"><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p class="text-sm text-gray-600">Quantity: <?php echo $item['quantity']; ?></p>
                                    <p class="text-blue-600 font-medium">Rp. <?php echo number_format($item['price']); ?></p>
                                </div>
                                
                                <div class="text-right">
                                    <p class="font-semibold">Subtotal</p>
                                    <p class="text-gray-600">Rp. <?php echo number_format($item['price'] * $item['quantity']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column - Payment Details -->
        <div class="lg:w-1/3">
            <div class="bg-white rounded-xl shadow-sm p-6 sticky top-4">
                <h2 class="text-2xl font-bold mb-4">Payment Details</h2>
                
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

                <?php if (!empty($items)): ?>
                    <form id="checkoutForm" class="space-y-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                                <div class="relative">
                                    <input type="text" 
                                           name="card_number" 
                                           class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="1234 5678 9012 3456"
                                           pattern="\d*"
                                           maxlength="16"
                                           required>
                                    <i class="fas fa-credit-card absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                                    <input type="text" 
                                           name="expiry" 
                                           class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="MM/YY"
                                           pattern="\d\d/\d\d"
                                           maxlength="5"
                                           required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                                    <input type="text" 
                                           name="cvv" 
                                           class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="123"
                                           pattern="\d*"
                                           maxlength="3"
                                           required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name on Card</label>
                                <input type="text" 
                                       name="card_name" 
                                       class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="John Doe"
                                       required>
                            </div>
                        </div>

                        <button type="submit" 
                                class="w-full py-4 px-6 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center space-x-2">
                            <span class="button-text">Pay Rp. <?php echo number_format($total); ?></span>
                            <span class="loading-text hidden">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Processing...
                            </span>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        let isSubmitting = false;

        // Format card number input
        const cardInput = checkoutForm.querySelector('input[name="card_number"]');
        cardInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value;
        });

        // Format expiry date input
        const expiryInput = checkoutForm.querySelector('input[name="expiry"]');
        expiryInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0, 2) + '/' + value.slice(2);
            }
            e.target.value = value;
        });

        // Format CVV input
        const cvvInput = checkoutForm.querySelector('input[name="cvv"]');
        cvvInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value;
        });

        // Handle form submission
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (isSubmitting) return;
            
            const submitBtn = checkoutForm.querySelector('button[type="submit"]');
            const buttonText = submitBtn.querySelector('.button-text');
            const loadingText = submitBtn.querySelector('.loading-text');
            
            // Show loading state
            isSubmitting = true;
            submitBtn.disabled = true;
            buttonText.classList.add('hidden');
            loadingText.classList.remove('hidden');

            // Create order
            fetch('api/orders/create.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    card_number: cardInput.value,
                    expiry: expiryInput.value,
                    cvv: cvvInput.value,
                    card_name: checkoutForm.querySelector('input[name="card_name"]').value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Redirect to order confirmation page
                    window.location.href = `order-confirmation.php?id=${data.orderId}`;
                } else {
                    throw new Error(data.message || 'Failed to place order');
                }
            })
            .catch(error => {
                showNotification(error.message, 'error');
                // Reset button state
                isSubmitting = false;
                submitBtn.disabled = false;
                buttonText.classList.remove('hidden');
                loadingText.classList.add('hidden');

                // If stock error, redirect to cart
                if (error.message.includes('stock')) {
                    setTimeout(() => {
                        window.location.href = 'cart.php';
                    }, 2000);
                }
            });
        });

        // Prevent accidental navigation
        window.addEventListener('beforeunload', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    }
});

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
