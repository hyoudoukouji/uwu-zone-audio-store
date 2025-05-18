<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$currentUser = $auth->getCurrentUser();
?>

<header class="flex justify-between items-center mb-8">
    <div class="flex-1 max-w-xl">
        <div class="relative">
            <input type="text" placeholder="Search Product" class="search-input w-full pl-10 pr-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:border-blue-500">
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
    </div>
    <div class="flex items-center space-x-4 ml-4">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/cart.php" class="header-icon p-2 rounded-lg relative">
                <i class="fas fa-shopping-cart text-gray-600"></i>
                <?php
                // Get cart count
                $stmt = $db->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $cartCount = $stmt->fetchColumn() ?: 0;
                if ($cartCount > 0):
                ?>
                <span id="cart-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center"><?php echo $cartCount; ?></span>
                <?php endif; ?>
            </a>
            <a href="/saved.php" class="header-icon p-2 rounded-lg relative">
                <i class="fas fa-heart text-gray-600"></i>
                <?php
                // Get wishlist count
                $stmt = $db->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $wishlistCount = $stmt->fetchColumn();
                if ($wishlistCount > 0):
                ?>
                <span id="wishlist-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center"><?php echo $wishlistCount; ?></span>
                <?php endif; ?>
            </a>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="relative group">
                <button class="flex items-center space-x-2">
                    <img src="<?php echo htmlspecialchars($_SESSION['profile_image'] ?? 'assets/images/default-avatar.png'); ?>" 
                         alt="Profile" 
                         class="w-10 h-10 rounded-full object-cover border-2 border-gray-200"
                         onerror="this.src='assets/images/default-avatar.png'">
                    <span class="text-sm font-medium text-gray-700"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </button>
                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden group-hover:block">
                    <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user mr-2"></i>Profile
                    </a>
                    <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-cog mr-2"></i>Settings
                    </a>
                    <hr class="my-2 border-gray-200">
                    <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        <?php else: ?>
            <button onclick="showAuthModal('login')" 
                    class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition-colors">
                <i class="fas fa-user text-gray-600 hover:text-blue-600 transition-colors"></i>
            </button>
        <?php endif; ?>
    </div>
</header>

<!-- Auth Modal -->
<?php include __DIR__ . '/auth-modal-new.php'; ?>
