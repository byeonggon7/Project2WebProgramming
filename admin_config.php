<?php
/**
 * admin_config.php
 * 
 * Admin panel to configure system-wide settings for the Fifteen Puzzle game.
 * Allows setting default puzzle size and maximum moves allowed to win.
 */

session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Fetch user role and ensure they are admin
$stmt = $pdo->prepare("SELECT role FROM users WHERE username = ?");
$stmt->execute([$_SESSION['username']]);
$user = $stmt->fetch();

// Deny access if not admin
if (!$user || $user['role'] !== 'admin') {
    echo "Access denied.";
    exit;
}

// Fetch current configuration from system_config table (assuming single row exists)
$config = $pdo->query("SELECT * FROM system_config LIMIT 1")->fetch(PDO::FETCH_ASSOC);

// Handle saving configuration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form inputs with defaults
    $default_size = $_POST['default_puzzle_size'] ?? '4x4';
    $max_moves = intval($_POST['max_moves']);

    // Clear existing config and insert new settings
    $pdo->exec("DELETE FROM system_config");
    $stmt = $pdo->prepare("INSERT INTO system_config (default_puzzle_size, max_moves)
                           VALUES (?, ?)");
    $stmt->execute([$default_size, $max_moves]);

    // Redirect to avoid form resubmission
    header("Location: admin_config.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - System Configuration</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        form { width: 400px; margin: auto; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input[type='text'], input[type='number'] { width: 100%; padding: 8px; }
        button { margin-top: 20px; padding: 10px 20px; cursor: pointer; }
        .back-link { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">System Configuration</h2>

    <!-- Configuration form with default puzzle size and max move settings -->
    <form method="POST">
        <label>Default Puzzle Size:</label>
        <input type="text" name="default_puzzle_size" value="<?= htmlspecialchars($config['default_puzzle_size'] ?? '4x4') ?>">

        <label>Max Moves to Win:</label>
        <input type="number" name="max_moves" value="<?= htmlspecialchars($config['max_moves'] ?? 1000) ?>">

        <button type="submit">Save Settings</button>
    </form>

    <!-- Back to admin dashboard link -->
    <div class="back-link">
        <a href="admin.php">&larr; Back to Admin Dashboard</a>
    </div>
</body>
</html>
