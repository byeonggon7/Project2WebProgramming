<?php
require 'db.php'; // Include the database connection file
session_start();  // Start the session

// Redirect to login page if the user is not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Get the current logged-in user's ID
$user_id = $_SESSION["user_id"];

// Prepare and execute SQL query to fetch game stats for the user, ordered by latest game
$stmt = $pdo->prepare("SELECT * FROM game_stats WHERE user_id = ? ORDER BY game_date DESC");
$stmt->execute([$user_id]);
$games = $stmt->fetchAll(); // Fetch all game records
?>

<!-- Display the user's game history in a table format -->
<h2>Your Game History</h2>
<table border="1">
<tr><th>Date</th><th>Size</th><th>Time</th><th>Moves</th><th>Win</th></tr>
<?php foreach ($games as $g): ?>
<tr>
  <td><?= $g["game_date"] ?></td>              <!-- Game date -->
  <td><?= $g["puzzle_size"] ?></td>            <!-- Puzzle size (e.g., 4x4) -->
  <td><?= $g["time_taken_seconds"] ?>s</td>    <!-- Time taken to complete -->
  <td><?= $g["moves_count"] ?></td>            <!-- Number of moves used -->
  <td><?= $g["win_status"] ? "Yes" : "No" ?></td> <!-- Win status -->
</tr>
<?php endforeach; ?>
</table>
