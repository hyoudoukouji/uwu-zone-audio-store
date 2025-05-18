<?php
require_once 'config/database.php';
require_once 'config/auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

// Perform logout
$auth->logout();

// Redirect to login page
header('Location: login.php');
exit;
?>
