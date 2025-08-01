<?php
// Start the session
session_start();

// Connect to the MySQL database
$db = new mysqli('localhost', 'bkim71', 'bkim71', 'bkim71');

// Initialize error message variable
$error = '';

// Handle the form submission when user clicks "Register"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve input values
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Check if all fields are filled out
    if (!empty($username) && !empty($email) && !empty($password)) {
        // Check if the username already exists
        $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Username is already in use
            $error = 'Username already taken.';
        } else {
            // Hash the password securely
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'player'; // Default role for new users

            // Insert new user into the database
            $insert = $db->prepare('INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)');
            $insert->bind_param('ssss', $username, $email, $hash, $role);
            $insert->execute();

            // Log the user in by setting session
            $_SESSION['username'] = $username;

            // Redirect to the main game
            header('Location: fifteen.php');
            exit();
        }
    } else {
        // Display error if any field is empty
        $error = 'Please fill out all fields.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="fifteen.css">
    <style>
        /* Page and form styling */
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
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            width: 100%;
            background: #28a745;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #218838;
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

<!-- Registration Form -->
<form class="form-container" method="POST" action="register.php">
    <h2>Register</h2>

    <!-- Display error if exists -->
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Input fields -->
    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>

    <!-- Submit registration -->
    <input type="submit" value="Register">

    <!-- Switch to login link -->
    <div class="switch-link">
        Already have an account? <a href="login.php">Login</a>
    </div>
</form>

</body>
</html>
