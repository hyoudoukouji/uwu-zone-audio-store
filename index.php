<?php
session_start();
require_once 'config/database.php';

// Database connection function
function getDatabaseConnection() {
    static $db = null;
    if ($db === null) {
        $database = new Database();
        $db = $database->getConnection();
    }
    return $db;
}

// Get featured product
function getFeaturedProduct() {
    try {
        $db = getDatabaseConnection();
        $query = "SELECT * FROM products WHERE featured = 1 LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error getting featured product: " . $e->getMessage());
        return null;
    }
}

// Get top selling products
function getTopSellingProducts($limit = 4) {
    try {
        $db = getDatabaseConnection();
        $query = "SELECT * FROM products ORDER BY sales_count DESC LIMIT :limit";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error getting top selling products: " . $e->getMessage());
        return [];
    }
}

// Get cart count
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

// Get featured and top selling products
$featuredProduct = getFeaturedProduct();
$topSellingProducts = getTopSellingProducts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UwU Zone - Audio Equipment Store</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg fixed h-full sidebar">
            <div class="p-4">
                <div class="flex items-center space-x-2 mb-8">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-headphones-alt text-white text-xl"></i>
                    </div>
                    <h1 class="text-xl font-bold text-blue-600">UwU Zone</h1>
                </div>
                
                <nav class="space-y-4">
                    <a href="index.php" class="sidebar-link active flex items-center space-x-3 p-3 rounded-lg">
                        <i class="fas fa-home sidebar-icon"></i>
                        <span>Home</span>
                    </a>
                    <a href="explore.php" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg">
                        <i class="fas fa-compass sidebar-icon"></i>
                        <span>Explore</span>
                    </a>
                    <a href="saved.php" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg">
                        <i class="fas fa-heart sidebar-icon"></i>
                        <span>Saved</span>
                    </a>
                    <a href="#" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg">
                        <i class="fas fa-shopping-cart sidebar-icon"></i>
                        <span>Cart</span>
                        <?php if ($cartCount > 0): ?>
                        <span id="cart-count" class="bg-blue-500 text-white text-xs rounded-full px-2 py-1"><?php echo $cartCount; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="#" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg">
                        <i class="fas fa-wallet sidebar-icon"></i>
                        <span>Wallet</span>
                    </a>
                    <a href="#" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg">
                        <i class="fas fa-user sidebar-icon"></i>
                        <span>Profile</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="ml-64 flex-1 p-8">
            <!-- Header -->
            <header class="flex justify-between items-center mb-8">
                <div class="flex-1 max-w-xl">
                    <div class="relative">
                        <input type="text" placeholder="Search Product" class="search-input w-full pl-10 pr-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:border-blue-500">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                <div class="flex items-center space-x-4 ml-4">
                    <button class="header-icon p-2 rounded-lg">
                        <i class="fas fa-envelope text-gray-600"></i>
                    </button>
                    <button class="header-icon p-2 rounded-lg">
                        <i class="fas fa-bell text-gray-600"></i>
                    </button>
                    <?php if (isset($_SESSION['user'])): ?>
                        <img src="<?php echo htmlspecialchars($_SESSION['user']['avatar']); ?>" alt="Profile" class="w-10 h-10 rounded-full">
                    <?php else: ?>
                        <button onclick="showAuthModal('login')" 
                                class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition-colors">
                            <i class="fas fa-user text-gray-600 hover:text-blue-600 transition-colors"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </header>

            <!-- Hero Section -->
            <div class="mb-12 animate-fade-in">
                <h1 class="text-3xl font-bold mb-2">In Ear Monitor</h1>
                <p class="text-gray-600">Built for the Beat, Tuned for You</p>
            </div>

            <!-- Featured Product -->
            <?php if ($featuredProduct): ?>
            <div class="bg-white rounded-2xl shadow-md p-6 mb-12 animate-fade-in">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($featuredProduct['name']); ?></h2>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <i class="fas fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="ml-2 text-gray-600">(<?php echo number_format($featuredProduct['review_count']); ?>+ Reviews)</span>
                        </div>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($featuredProduct['description']); ?></p>
                        <p class="text-2xl font-bold text-blue-600 mb-4">Rp. <?php echo number_format($featuredProduct['price']); ?></p>
                        <div class="flex items-center space-x-4">
                            <button class="btn-primary px-6 py-2 border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50"
                                    data-action="toggle-wishlist"
                                    data-product-id="<?php echo $featuredProduct['id']; ?>">
                                <i class="fas fa-heart"></i>
                            </button>
                            <button class="btn-primary px-6 py-2 border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50"
                                    data-action="add-to-cart"
                                    data-product-id="<?php echo $featuredProduct['id']; ?>">
                                Add to cart
                            </button>
                            <button class="btn-primary px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                                    data-action="buy-now"
                                    data-product-id="<?php echo $featuredProduct['id']; ?>">
                                Buy now
                            </button>
                        </div>
                    </div>
                    <img src="<?php echo htmlspecialchars($featuredProduct['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($featuredProduct['name']); ?>" 
                         class="w-72 object-contain product-image">
                </div>
            </div>
            <?php endif; ?>

            <!-- Popular Products -->
            <div class="mb-12">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Top Seller</h2>
                    <a href="explore.php" class="text-blue-600 hover:underline">View all</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ($topSellingProducts as $index => $product): ?>
                    <div class="product-card bg-white rounded-xl shadow-md p-4 animate-fade-in" 
                         style="animation-delay: <?php echo $index * 0.1; ?>s">
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="block">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 class="w-full h-48 object-contain mb-4 product-image">
                            <h3 class="font-semibold mb-2 hover:text-blue-600 transition-colors duration-300">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </h3>
                        </a>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <i class="fas fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="ml-2 text-sm text-gray-600"><?php echo $product['review_count']; ?> Reviews</span>
                        </div>
                        <p class="text-blue-600 font-bold">Rp. <?php echo number_format($product['price']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Categories Section -->
            <div class="mb-12">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Explore Popular Categories</h2>
                    <a href="explore.php" class="text-blue-600 hover:underline">See all</a>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div class="category-card bg-white rounded-xl shadow-md p-6 animate-fade-in" style="animation-delay: 0.5s">
                        <h3 class="text-lg font-semibold mb-2">Popular top 10 brands</h3>
                        <p class="text-gray-600 mb-4">5,400+ Orders & reviews</p>
                        <div class="flex space-x-2">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-star text-blue-500"></i>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                <i class="fas fa-crown text-purple-500"></i>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                        </div>
                    </div>
                    <div class="category-card bg-white rounded-xl shadow-md p-6 animate-fade-in" style="animation-delay: 0.6s">
                        <h3 class="text-lg font-semibold mb-2">Newest Customers</h3>
                        <p class="text-gray-600 mb-4">4,600+ reviews</p>
                        <div class="flex space-x-2">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-user text-blue-500"></i>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-user text-green-500"></i>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                <i class="fas fa-user text-yellow-500"></i>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                <i class="fas fa-user text-purple-500"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Auth Modal -->
    <?php include 'partials/auth-modal-new.php'; ?>

    <!-- Custom JavaScript -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/auth-modal-new.js"></script>
</body>
</html>
