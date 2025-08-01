<?php
/**
 * admin_backgrounds.php
 * 
 * Admin panel to manage background images for the Fifteen Puzzle game.
 * Admins can upload, edit, delete, and toggle activation status of backgrounds.
 */

session_start();
require_once 'db.php';

// Verify user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Check if logged-in user has admin privileges
$stmt = $pdo->prepare("SELECT role FROM users WHERE username = ?");
$stmt->execute([$_SESSION['username']]);
$user = $stmt->fetch();

// Deny access if user is not admin
if (!$user || $user['role'] !== 'admin') {
    echo "Access denied.";
    exit;
}

// Handle form submissions (CRUD actions)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $imageId = $_POST['image_id'] ?? null;

    // Toggle image active/inactive status
    if ($action === 'toggle_status' && $imageId) {
        $stmt = $pdo->prepare("UPDATE background_images SET is_active = NOT is_active WHERE image_id = ?");
        $stmt->execute([$imageId]);

    // Edit image name
    } elseif ($action === 'edit' && $imageId) {
        $newName = $_POST['new_name'] ?? '';
        $stmt = $pdo->prepare("UPDATE background_images SET image_name = ? WHERE image_id = ?");
        $stmt->execute([$newName, $imageId]);

    // Delete image
    } elseif ($action === 'delete' && $imageId) {
        $stmt = $pdo->prepare("DELETE FROM background_images WHERE image_id = ?");
        $stmt->execute([$imageId]);

    // Upload a new background image
    } elseif ($action === 'upload') {
        $name = $_POST['image_name'] ?? '';
        $url = $_POST['image_url'] ?? '';
        $uploadBy = $user['user_id'];
        $stmt = $pdo->prepare("INSERT INTO background_images (image_name, image_url, uploaded_by_user_id) VALUES (?, ?, ?)");
        $stmt->execute([$name, $url, $uploadBy]);
    }
}

// Fetch all background images to display
$images = $pdo->query("SELECT * FROM background_images")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Background Images</title>
    <style>
        table { width: 90%; margin: auto; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        input[type='text'] { width: 150px; }
        form { display: inline-block; margin: 5px; }
        h1 { text-align: center; }
    </style>
</head>
<body>

    <h1>Background Image Management</h1>
    <a href="admin.php">&larr; Back to Admin Dashboard</a>

    <!-- Upload new background form -->
    <h3 style="text-align:center;">Upload New Background</h3>
    <form method="POST" style="text-align:center;">
        <input type="hidden" name="action" value="upload">
        Name: <input type="text" name="image_name" required>
        URL: <input type="text" name="image_url" required>
        <input type="submit" value="Upload">
    </form>

    <!-- Display table of backgrounds with action buttons -->
    <table>
        <tr>
            <th>ID</th>
            <th>Preview</th>
            <th>Name</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php foreach ($images as $img): ?>
            <tr>
                <td><?= $img['image_id'] ?></td>

                <!-- Show image preview -->
                <td><img src="<?= htmlspecialchars($img['image_url']) ?>" width="100" height="100"></td>

                <!-- Edit image name -->
                <td>
                    <form method="POST">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="image_id" value="<?= $img['image_id'] ?>">
                        <input type="text" name="new_name" value="<?= htmlspecialchars($img['image_name']) ?>">
                        <input type="submit" value="Update Name">
                    </form>
                </td>

                <!-- Show active/inactive status -->
                <td><?= $img['is_active'] ? 'Active' : 'Inactive' ?></td>

                <!-- Toggle status or delete image -->
                <td>
                    <form method="POST">
                        <input type="hidden" name="action" value="toggle_status">
                        <input type="hidden" name="image_id" value="<?= $img['image_id'] ?>">
                        <input type="submit" value="<?= $img['is_active'] ? 'Deactivate' : 'Activate' ?>">
                    </form>

                    <form method="POST" onsubmit="return confirm('Delete image?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="image_id" value="<?= $img['image_id'] ?>">
                        <input type="submit" value="Delete">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>
