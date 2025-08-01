<?php
/**
 * fifteen.php
 * 
 * Main interface for the Fifteen Puzzle game.
 * This file initializes the game page after verifying session authentication,
 * fetches background image options from the database, and renders the game UI.
 * For the 2 extra features, I implemented cheat button and game time with music file.
 *
 */

// Start session and include database connection
session_start();
require_once 'db.php';

// Redirect to login page if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Retrieve username from session
$username = $_SESSION['username'];

// Fetch user_id from database using username
$stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();
$user_id = $user ? $user['user_id'] : null;

// Fetch available background images that are active
$stmt = $pdo->prepare("SELECT * FROM background_images WHERE is_active = 1");
$stmt->execute();
$backgrounds = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fifteen Puzzle Game</title>

    <!-- Page styling -->
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: linear-gradient(135deg, #f6f9fc, #e9eff5);
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        h1 {
            font-size: 36px;
            margin: 20px 0 10px;
            color: #333;
        }
        h2 {
            font-size: 24px;
            margin: 10px 0 5px;
            color: #444;
        }
        p.instructions {
            font-size: 16px;
            max-width: 600px;
            text-align: center;
            color: #555;
            background: #fff;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        #puzzlearea {
            width: 400px;
            height: 400px;
            margin: 20px 0;
            position: relative;
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .tile {
            width: 100px;
            height: 100px;
            position: absolute;
            background-size: 400px 400px;
            font-size: 28px;
            font-weight: bold;
            color: white;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.4);
            text-align: center;
            line-height: 100px;
            border-radius: 10px;
            background-color: #3498db;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            cursor: pointer;
            user-select: none;
        }
        select, button {
            margin: 8px;
            padding: 12px 16px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ccc;
            background-color: #fff;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }
        select:hover, button:hover {
            background-color: #f0f0f0;
        }
        #moveCount, #timer {
            font-size: 18px;
            margin: 8px;
            color: #444;
        }
    </style>
</head>
<body>

    <!-- Game heading -->
    <h1>Fifteen Puzzle Game</h1>
    <h2>Welcome, <?= htmlspecialchars($username) ?>!</h2>

    <!-- Game instructions -->
    <p style="margin-top: -10px; font-size: 16px; max-width: 600px; margin-left: auto; margin-right: auto;">
        Fifteen Puzzle is a classic sliding puzzle game where you arrange the tiles in order from 1 to 15, starting from the top-left and ending at the bottom-right. Click a tile adjacent to the empty space to slide it. Try to solve the puzzle in the least number of moves and time!
    </p>

    <!-- Background selector dropdown -->
    <select id="backgroundSelector">
        <?php foreach ($backgrounds as $bg): ?>
            <option value="https://codd.cs.gsu.edu/~bkim71/FifteenPuzzle/images/<?= htmlspecialchars($bg['image_url']) ?>" data-id="<?= $bg['image_id'] ?>">
                <?= htmlspecialchars($bg['image_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- Game control buttons -->
    <br>
    <button id="startBtn">Start Game</button>
    <button id="cheatBtn">Cheat</button>
    <button id="musicToggle">Play Music</button>

    <!-- Puzzle grid container -->
    <div id="puzzlearea"></div>

    <!-- Game stats display -->
    <p>Moves: <span id="moveCount">0</span></p>
    <p>Time: <span id="timer">0</span></p>

    <!-- Hidden input to hold user_id for saving game stats -->
    <input type="hidden" id="userId" value="<?= htmlspecialchars($user_id) ?>">

    <!-- Background music element -->
    <audio id="bgMusic" loop>
        <source src="theme.mp3" type="audio/mpeg">
    </audio>

    <!-- Congratulations overlay (initially hidden) -->
    <div id="congrats-message" style="display: none; 
        position: fixed; 
        top: 40%; 
        left: 50%; 
        transform: translate(-50%, -50%);
        background: #28a745; 
        color: #fff; 
        padding: 20px 40px; 
        font-size: 24px; 
        border-radius: 10px; 
        box-shadow: 0 0 10px rgba(0,0,0,0.5); 
        z-index: 999;">
        🎉 Congratulations! You solved the puzzle! 🎉
    </div>

    <!-- JavaScript to sync background image selection with game -->
    <script>
        window.backgroundImage = document.getElementById("backgroundSelector").value;
        document.getElementById("backgroundSelector").addEventListener("change", function () {
            window.backgroundImage = this.value;
        });
    </script>

    <!-- Main game script -->
    <script src="fifteen.js"></script>
</body>
</html>
