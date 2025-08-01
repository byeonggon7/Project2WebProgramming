<?php
/**
 * Admin - Game Stats Dashboard
 * Displays overall game statistics and top players summary.
 */

session_start();
require_once 'db.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Fetch user role to verify admin privileges
$stmt = $pdo->prepare("SELECT user_id, role FROM users WHERE username = ?");
$stmt->execute([$_SESSION['username']]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'admin') {
    echo "Access denied.";
    exit;
}

// Fetch aggregate game statistics
$totalGames = $pdo->query("SELECT COUNT(*) FROM game_stats")->fetchColumn();
$avgTime = $pdo->query("SELECT AVG(time_taken_seconds) FROM game_stats")->fetchColumn();
$avgMoves = $pdo->query("SELECT AVG(moves_count) FROM game_stats")->fetchColumn();

// Determine the most popular puzzle size by usage frequency
$popularPuzzle = $pdo->query("
    SELECT puzzle_size, COUNT(*) AS count 
    FROM game_stats 
    GROUP BY puzzle_size 
    ORDER BY count DESC 
    LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

// Fetch top 10 players based on games played
$topPlayers = $pdo->query("
    SELECT u.username, COUNT(*) AS games_played, 
           AVG(g.time_taken_seconds) AS avg_time, 
           AVG(g.moves_count) AS avg_moves
    FROM users u
    JOIN game_stats g ON u.user_id = g.user_id
    GROUP BY u.user_id
    ORDER BY games_played DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Game Stats</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        table { width: 90%; margin: auto; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        h2, h3 { text-align: center; }
        .back-link { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <h2>Game Statistics Overview</h2>

    <!-- Table for aggregate game metrics -->
    <table>
        <tr><th>Total Games</th><td><?= $totalGames ?></td></tr>
        <tr><th>Average Time (sec)</th><td><?= round($avgTime, 2) ?></td></tr>
        <tr><th>Average Moves</th><td><?= round($avgMoves, 2) ?></td></tr>
        <tr><th>Most Popular Puzzle Size</th><td><?= htmlspecialchars($popularPuzzle['puzzle_size'] ?? '-') ?></td></tr>
    </table>

    <!-- Top 10 Players based on most games played -->
    <h3>Top Players</h3>
    <table>
        <tr>
            <th>Username</th>
            <th>Games Played</th>
            <th>Avg Time (s)</th>
            <th>Avg Moves</th>
        </tr>
        <?php foreach ($topPlayers as $player): ?>
            <tr>
                <td><?= htmlspecialchars($player['username']) ?></td>
                <td><?= $player['games_played'] ?></td>
                <td><?= round($player['avg_time'], 2) ?></td>
                <td><?= round($player['avg_moves'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Back link to admin dashboard -->
    <div class="back-link">
        <a href="admin.php">&larr; Back to Admin Dashboard</a>
    </div>
</body>
</html>
