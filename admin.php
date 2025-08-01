<?php
/**
 * admin_dashboard.php
 * 
 * Displays admin dashboard with links to manage users, backgrounds,
 * view stats, and configure system settings. Only accessible to admin users.
 */

// Start session and include database connection
session_start();
require_once 'db.php';

// Redirect to login page if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Fetch user role to confirm admin access
$stmt = $pdo->prepare("SELECT role FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

// Redirect non-admin users to the game
if (!$user || $user['role'] !== 'admin') {
    header("Location: fifteen.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        /* General body styling */
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f3f3f3;
        }

        /* Page title styling */
        h1 {
            margin-top: 50px;
        }

        /* Emoji crown size */
        .crown {
            font-size: 24px;
        }

        /* Container for admin navigation buttons */
        .admin-options {
            margin-top: 40px;
        }

        /* Style for each admin page link */
        .admin-options a {
            display: inline-block;
            margin: 10px 20px;
            padding: 15px 30px;
            background-color: #222;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            transition: background-color 0.2s ease;
        }

        /* Hover effect for buttons */
        .admin-options a:hover {
            background-color: #555;
        }
    </style>
</head>
<body>

    <!-- Header with crown emoji for admin -->
    <h1>Welcome, Admin <span class="crown">👑</span></h1>

    <!-- Admin control panel links -->
    <div class="admin-options">
        <a href="admin_users.php">👥 Manage Users</a>
        <a href="admin_backgrounds.php">🖼️ Manage Backgrounds</a>
        <a href="admin_stats.php">📊 View Statistics</a>
        <a href="admin_config.php">⚙️ System Settings</a>
        <a href="fifteen.php" style="text-decoration: none; font-size: 16px;">⬅️ Back to Game</a>
    </div>

</body>
</html>
