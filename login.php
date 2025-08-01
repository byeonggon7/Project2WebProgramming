<?php
// Start the session
session_start();

// Connect to the MySQL database
$db = new mysqli('localhost', 'bkim71', 'bkim71', 'bkim71');

$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve input values safely
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Prepare SQL query to get user data by username
    $stmt = $db->prepare('SELECT username, password_hash, role FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if user exists and verify password
    if ($user && password_verify($password, $user['password_hash'])) {
        // Set session with username
        $_SESSION['username'] = $username;

        // Redirect based on user role
        if ($user['role'] === 'admin') {
            header('Location: admin.php');
        } else {
            header('Location: fifteen.php');
        }
        exit();
    } else {
        // Set error message on failure
        $error = 'Invalid username or password.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <!-- Optional external CSS -->
    <link rel="stylesheet" href="fifteen.css">
    <style>
        /* Style the login page layout */
        body {
            font-family: Arial, sans-serif;
            background: #f1f1f1;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            width: 320px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            width: 100%;
            background: #007BFF;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #0056b3;
        }
        .switch-link {
            text-align: center;
            margin-top: 10px;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<!-- Login Form UI -->
<form class="form-container" method="POST" action="login.php">
    <h2>Login</h2>

    <!-- Display error message if login fails -->
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Username and Password Inputs -->
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>

    <!-- Login Button -->
    <input type="submit" value="Login">

    <!-- Link to register -->
    <div class="switch-link">
        Don't have an account? <a href="register.php">Register</a>
    </div>
</form>

</body>
</html>
