<?php
/**
 * Admin stats and background image management dashboard.
 * This script displays user accounts, game statistics, and background image controls.
 */

require 'db.php';
session_start();

// Ensure only logged-in admin can access this page
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

// Fetch all users from the database
$users = $pdo->query("SELECT user_id, username, email, role, last_login FROM users")->fetchAll();

// Fetch summary stats from the game_stats table
$summary = $pdo->query("
    SELECT COUNT(*) AS total_games,
           AVG(time_taken_seconds) AS avg_time,
           AVG(moves_count) AS avg_moves
    FROM game_stats
")->fetch();
?>

<!-- Admin dashboard starts -->
<h2>Admin Dashboard</h2>

<p><a href="logout.php">Logout</a></p>

<!-- List all user accounts -->
<h3>🧑 User Accounts</h3>
<table border="1">
<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Last Login</th></tr>
<?php foreach ($users as $u): ?>
<tr>
  <td><?= $u["user_id"] ?></td>
  <td><?= htmlspecialchars($u["username"]) ?></td>
  <td><?= htmlspecialchars($u["email"]) ?></td>
  <td><?= $u["role"] ?></td>
  <td><?= $u["last_login"] ?: "Never" ?></td>
</tr>
<?php endforeach; ?>
</table>

<!-- Show global statistics summary -->
<h3>📊 Global Game Stats</h3>
<ul>
  <li>Total Games Played: <?= $summary["total_games"] ?></li>
  <li>Average Time: <?= round($summary["avg_time"]) ?> seconds</li>
  <li>Average Moves: <?= round($summary["avg_moves"]) ?></li>
</ul>

<!-- Image upload form for background selection -->
<h3>🖼 Background Image Upload</h3>
<form method="POST" enctype="multipart/form-data" action="upload_image.php">
  Select image: <input type="file" name="image" accept=".png,.jpg,.jpeg" required>
  <input type="submit" value="Upload">
</form>

<!-- Manage existing background images -->
<h3>🎨 Manage Background Images</h3>

<table border="1">
<tr><th>ID</th><th>Name</th><th>Preview</th><th>Active</th><th>Action</th></tr>
<?php
$images = $pdo->query("SELECT * FROM background_images ORDER BY image_id DESC")->fetchAll();
foreach ($images as $img): ?>
<tr>
  <td><?= $img["image_id"] ?></td>
  <td><?= htmlspecialchars($img["image_name"]) ?></td>
  <td><img src="<?= $img["image_url"] ?>" width="80"></td>
  <td><?= $img["is_active"] ? "Yes" : "No" ?></td>
  <td>
    <!-- Form to toggle activation status of background -->
    <form method="POST" action="toggle_image.php" style="display:inline;">
      <input type="hidden" name="image_id" value="<?= $img["image_id"] ?>">
      <input type="submit" value="<?= $img["is_active"] ? 'Disable' : 'Enable' ?>">
    </form>
  </td>
</tr>
<?php endforeach; ?>
</table>
