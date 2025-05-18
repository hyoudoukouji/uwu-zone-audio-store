<?php
session_start();
require_once 'config/database.php';
require_once 'config/auth.php';

// Initialize auth
$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

// Get product ID from URL
$productId = $_GET['id'] ?? null;

if (!$productId) {
    header('Location: index.php');
    exit;
}

// Get product details
try {
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header('Location: index.php');
        exit;
    }

    // Get related products
    $query = "SELECT * FROM products WHERE id != ? ORDER BY sales_count DESC LIMIT 4";
    $stmt = $db->prepare($query);
    $stmt->execute([$productId]);
    $relatedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if product is in wishlist for logged in user
    $isWishlisted = false;
    if (isset($_SESSION['user_id'])) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$_SESSION['user_id'], $productId]);
        $isWishlisted = (bool)$stmt->fetchColumn();
    }

    $pageTitle = $product['name'] . ' - UwU Zone';

    // Start output buffering
    ob_start();
?>

<!-- Product Details -->
<div class="bg-white rounded-xl shadow-sm p-8 flex gap-8 mb-8">
    <div class="flex flex-col w-1/2">
        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
             alt="<?php echo htmlspecialchars($product['name']); ?>" 
             class="rounded-lg mb-4 object-cover max-h-[400px]">
        <div class="flex gap-2">
            <?php for ($i = 0; $i < 4; $i++): ?>
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                     alt="Thumbnail" 
                     class="w-20 h-20 rounded-lg border border-gray-300 object-cover cursor-pointer hover:border-blue-500 transition-colors">
            <?php endfor; ?>
        </div>
    </div>
    <div class="flex flex-col w-1/2">
        <h1 class="text-3xl font-bold mb-4"><?php echo htmlspecialchars($product['name']); ?></h1>
        <div class="flex items-center mb-4">
            <div class="flex text-yellow-400">
                <?php for ($i = 0; $i < 5; $i++): ?>
                    <i class="fas fa-star"></i>
                <?php endfor; ?>
            </div>
            <span class="ml-2 text-gray-600">(<?php echo number_format($product['review_count']); ?> Reviews)</span>
        </div>
        <p class="text-gray-600 mb-8"><?php echo htmlspecialchars($product['description']); ?></p>
        <h2 class="text-[40px] font-bold text-blue-600 mb-4">Rp. <?php echo number_format($product['price']); ?></h2>
        <p class="text-gray-600 mb-6">Stock: <?php echo $product['stock']; ?> units available</p>
        <div class="flex items-center gap-2 mb-6">
            <button class="w-10 h-10 flex items-center justify-center border rounded-lg text-gray-600 hover:border-blue-600 transition-colors" onclick="updateQuantity(-1)">
                <i class="fas fa-minus"></i>
            </button>
            <input id="quantity" type="number" value="1" min="1" max="<?php echo $product['stock']; ?>" 
                   class="w-16 h-10 text-center border rounded-lg focus:outline-none focus:border-blue-500">
            <button class="w-10 h-10 flex items-center justify-center border rounded-lg text-gray-600 hover:border-blue-600 transition-colors" onclick="updateQuantity(1)">
                <i class="fas fa-plus"></i>
            </button>
        </div>
        <div class="flex items-center gap-3">
            <button class="flex-1 h-12 bg-white border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors text-sm font-medium"
                    data-action="add-to-cart"
                    data-product-id="<?php echo $product['id']; ?>">
                <i class="fas fa-shopping-cart mr-2"></i>Add to Cart
            </button>
            <button class="flex-1 h-12 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium"
                    data-action="buy-now"
                    data-product-id="<?php echo $product['id']; ?>">
                <i class="fas fa-bolt mr-2"></i>Buy Now
            </button>
            <button class="w-12 h-12 flex items-center justify-center border border-blue-600 rounded-lg hover:bg-blue-50 transition-colors"
                    data-action="toggle-wishlist"
                    data-product-id="<?php echo $product['id']; ?>">
                <i class="fas fa-heart <?php echo $isWishlisted ? 'text-red-500' : 'text-blue-600'; ?>"></i>
            </button>
        </div>
        <hr class="my-6 border-gray-200">
        <h3 class="font-semibold mb-2">Key Features</h3>
        <ul class="list-none space-y-1 text-gray-700">
            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>Full-Frequency Dynamic Driver</li>
            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>Dual Driver Two-way Crossover</li>
            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>UWBS Combination</li>
            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>Verified Acoustic FEA Optimization</li>
        </ul>
    </div>
</div>

<!-- Related Products -->
<div class="mb-8">
    <h2 class="text-2xl font-bold mb-6">Related Products</h2>
    <div class="grid grid-cols-4 gap-6">
        <?php foreach ($relatedProducts as $relatedProduct): ?>
        <div class="product-card bg-white rounded-xl shadow-md p-4">
            <a href="product.php?id=<?php echo $relatedProduct['id']; ?>" class="block">
                <img src="<?php echo htmlspecialchars($relatedProduct['image_url']); ?>" 
                     alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>" 
                     class="w-full h-48 object-contain mb-4 product-image">
                <h3 class="font-semibold mb-2 hover:text-blue-600 transition-colors duration-300">
                    <?php echo htmlspecialchars($relatedProduct['name']); ?>
                </h3>
            </a>
            <div class="flex items-center mb-2">
                <div class="flex text-yellow-400">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                        <i class="fas fa-star"></i>
                    <?php endfor; ?>
                </div>
                <span class="ml-2 text-sm text-gray-600"><?php echo $relatedProduct['review_count']; ?> Reviews</span>
            </div>
            <p class="text-blue-600 font-bold">Rp. <?php echo number_format($relatedProduct['price']); ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
    $pageContent = ob_get_clean();

    // Add extra scripts
    $isLoggedIn = isset($_SESSION['user_id']) ? 'true' : 'false';
    $extraScripts = <<<HTML
    <script>
        // Set login state for JavaScript
        window.isUserLoggedIn = {$isLoggedIn};
        
        function updateQuantity(change) {
            const input = document.getElementById('quantity');
            const newValue = Math.max(1, Math.min(parseInt(input.value) + change, parseInt(input.max)));
            input.value = newValue;
        }
    </script>
    <script src="/assets/js/product-actions.js"></script>
    HTML;

    // Include the layout
    require_once 'partials/layout.php';
} catch(PDOException $e) {
    header('Location: index.php');
    exit;
}
?>
