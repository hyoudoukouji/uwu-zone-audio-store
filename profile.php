<?php
require_once 'config/database.php';
require_once 'config/auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

$currentUser = $auth->getCurrentUser();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $auth->isLoggedIn() && $currentUser) {
    $data = [
        'username' => $_POST['username'] ?? $currentUser['username'],
        'email' => $_POST['email'] ?? $currentUser['email'],
        'address' => $_POST['address'] ?? $currentUser['address'] ?? '',
        'full_name' => $_POST['full_name'] ?? $currentUser['full_name'] ?? '',
        'phone_number' => $_POST['phone_number'] ?? $currentUser['phone_number'] ?? ''
    ];
    
    if ($auth->updateProfile($currentUser['id'], $data)) {
        $success = 'Profile updated successfully';
        $currentUser = $auth->getCurrentUser(); // Refresh user data
        if (!$currentUser) {
            $error = 'Failed to retrieve updated profile data';
        }
    } else {
        $error = 'Failed to update profile';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - UwU Zone</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'partials/header.php'; ?>
    
    <div class="min-h-screen">
        <div class="container mx-auto px-4 py-8">
            <?php if ($auth->isLoggedIn() && $currentUser): ?>
                <div class="max-w-7xl mx-auto">
                    <!-- Page Header -->
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Profile Settings</h1>
                        <nav class="flex space-x-4" aria-label="Tabs">
                            <a href="#profile" class="px-3 py-2 text-sm font-medium text-blue-700 rounded-md bg-blue-50">
                                Profile Info
                            </a>
                            <a href="settings.php" class="px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 rounded-md">
                                Account Settings
                            </a>
                        </nav>
                    </div>

                    <!-- Alert Messages -->
                    <?php if ($error): ?>
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline"><?php echo htmlspecialchars($success); ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- Main Content -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="md:grid md:grid-cols-3 md:gap-6">
                            <!-- Profile Image Section -->
                            <div class="md:col-span-1 bg-gray-50 px-4 py-6 sm:px-6 rounded-l-lg">
                                <div class="space-y-6">
                                    <div class="text-center">
                                        <div class="relative inline-block">
                                            <img src="<?php echo htmlspecialchars($currentUser['profile_image']); ?>" 
                                                 alt="Profile" 
                                                 class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-white shadow-lg"
                                                 onerror="this.src='assets/images/default-avatar.png'">
                                            <button class="absolute bottom-2 right-2 bg-blue-600 text-white rounded-full p-2 shadow-lg hover:bg-blue-700 transition-colors">
                                                <i class="fas fa-camera"></i>
                                            </button>
                                        </div>
                                        <h2 class="mt-4 text-xl font-semibold text-gray-900"><?php echo htmlspecialchars($currentUser['username']); ?></h2>
                                        <p class="text-gray-600"><?php echo htmlspecialchars($currentUser['email']); ?></p>
                                    </div>
                                    <div class="border-t border-gray-200 pt-4">
                                        <h3 class="text-sm font-medium text-gray-500">Member since</h3>
                                        <p class="mt-1 text-sm text-gray-900">January 2024</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Profile Form Section -->
                            <div class="md:col-span-2 px-4 py-6 sm:p-6">
                                <form method="POST" class="space-y-6">
                                    <div class="grid grid-cols-6 gap-6">
                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                                            <input type="text" name="username" id="username" 
                                                   value="<?php echo htmlspecialchars($currentUser['username']); ?>"
                                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        </div>

                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                            <input type="email" name="email" id="email" 
                                                   value="<?php echo htmlspecialchars($currentUser['email']); ?>"
                                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        </div>

                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                            <input type="text" name="full_name" id="full_name"
                                                   value="<?php echo htmlspecialchars($currentUser['full_name'] ?? ''); ?>"
                                                   placeholder="Enter your full name"
                                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        </div>

                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                            <input type="text" name="phone_number" id="phone_number"
                                                   value="<?php echo htmlspecialchars($currentUser['phone_number'] ?? ''); ?>"
                                                   placeholder="Enter your phone number"
                                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        </div>

                                        <div class="col-span-6">
                                            <label for="address" class="block text-sm font-medium text-gray-700">Shipping Address</label>
                                            <textarea name="address" id="address"
                                                   placeholder="Enter your shipping address"
                                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                   rows="3"><?php echo htmlspecialchars($currentUser['address'] ?? ''); ?></textarea>
                                        </div>
                                    </div>

                                    <div class="flex justify-end pt-4 border-t border-gray-200">
                                        <button type="submit" 
                                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                            <i class="fas fa-save mr-2"></i>
                                            Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8 text-center">
                    <img class="mx-auto w-48 h-48" src="assets/images/nyan-cat.svg" alt="Nyan Cat">
                    <h2 class="mt-6 text-2xl font-bold text-gray-900">Oops!</h2>
                    <p class="mt-2 text-gray-600">You are not logged in. Please log in to access your profile.</p>
                    <button onclick="showAuthModal()" 
                            class="mt-6 inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Login
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'partials/auth-modal-new.php'; ?>
    <script src="assets/js/auth-modal-new.js"></script>
</body>
</html>
