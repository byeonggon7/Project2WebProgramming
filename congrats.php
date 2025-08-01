<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Congratulations!</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #e0ffe0, #ccf5cc);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            color: #2d862d;
        }
        h1 {
            font-size: 48px;
        }
        p {
            font-size: 24px;
        }
        a {
            margin-top: 20px;
            text-decoration: none;
            font-size: 18px;
            background: #2d862d;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
        }
        a:hover {
            background: #256d25;
        }
    </style>
</head>
<body>
    <h1>🎉 Congratulations, <?= htmlspecialchars($_SESSION['username']) ?>! 🎉</h1>
    <p>You successfully completed the Fifteen Puzzle.</p>
    <a href="fifteen.php">Play Again</a>
</body>
</html>
