<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

// Get cart count
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'UwU Zone - Audio Equipment Store'; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">

    <?php if (isset($extraStyles)): ?>
        <?php echo $extraStyles; ?>
    <?php endif; ?>
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="ml-64 flex-1 p-8">
            <!-- Header -->
            <?php include 'header.php'; ?>

            <!-- Page Content -->
            <main>
                <?php echo $pageContent ?? ''; ?>
            </main>
        </div>
    </div>

    <!-- Auth Modal -->
    <?php include 'auth-modal-new.php'; ?>

    <script>
        window.isUserLoggedIn = <?php echo $auth->isLoggedIn() ? 'true' : 'false'; ?>;
    </script>

    <!-- Custom JavaScript -->
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/auth-modal-new.js"></script>
    
    <?php if (isset($extraScripts)): ?>
        <?php echo $extraScripts; ?>
    <?php endif; ?>
</body>
</html>
