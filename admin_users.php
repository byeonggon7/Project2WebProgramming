<?php
session_start();
require_once 'db.php';

// Check admin access
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT role FROM users WHERE username = ?");
$stmt->execute([$_SESSION['username']]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'admin') {
    echo "Access denied.";
    exit;
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($userId && $action) {
        if ($action === 'edit_username') {
            $newUsername = $_POST['new_username'] ?? '';
            if (!empty($newUsername)) {
                $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE user_id = ?");
                $stmt->execute([$newUsername, $userId]);
            }
        } elseif ($action === 'toggle_status') {
            $stmt = $pdo->prepare("UPDATE users SET is_active = NOT is_active WHERE user_id = ?");
            $stmt->execute([$userId]);
        } elseif ($action === 'reset_password') {
            // Set default reset password (e.g., 'password123'), hashed
            $defaultPassword = password_hash("password123", PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt->execute([$defaultPassword, $userId]);
        }
    }

    // Refresh to avoid resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Get all users
$users = $pdo->query("SELECT user_id, username, role, is_active FROM users")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fefefe;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        h1 {
            text-align: center;
        }
        form {
            display: inline-block;
        }
        input[type="text"] {
            width: 100px;
        }
        .btn {
            padding: 5px 10px;
            margin: 2px;
        }
        a {
            display: block;
            margin: 20px auto;
            width: fit-content;
            color: #4a0d84;
        }
    </style>
</head>
<body>
    <h1>User Account Management</h1>

    <!-- Link back to Admin Dashboard -->
    <a href="admin.php">← Back to Admin Dashboard</a>

    <!-- User table displaying each user's info and actions -->
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><?= $u['user_id'] ?></td>

            <!-- Inline username editing -->
            <td>
                <form method="post">
                    <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                    <input type="text" name="new_username" value="<?= htmlspecialchars($u['username']) ?>">
                    <button class="btn" type="submit" name="action" value="edit_username">Update</button>
                </form>
            </td>

            <td><?= htmlspecialchars($u['role']) ?></td>
            <td><?= $u['is_active'] ? 'Active' : 'Inactive' ?></td>

            <!-- Admin controls: activate/deactivate & reset password -->
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                    <button class="btn" name="action" value="toggle_status">
                        <?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>
                    </button>
                </form>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                    <button class="btn" name="action" value="reset_password">Reset Password</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
