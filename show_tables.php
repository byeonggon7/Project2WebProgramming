<?php
// Connect to MySQL database
$conn = new mysqli("localhost", "bkim71", "bkim71", "bkim71");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define list of tables to display
$tables = ["users", "background_images", "game_stats", "system_config", "user_preferences"];

// Loop through each table
foreach ($tables as $table) {
    echo "<h2>Table: $table</h2>";

    // Execute SELECT query for the current table
    $result = $conn->query("SELECT * FROM $table");

    // Check if the table has rows
    if ($result && $result->num_rows > 0) {
        echo "<table border='1'><tr>";

        // Output table headers
        while ($fieldinfo = $result->fetch_field()) {
            echo "<th>{$fieldinfo->name}</th>";
        }
        echo "</tr>";

        // Output table data rows
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $cell) {
                // Escape HTML for safe output
                echo "<td>" . htmlspecialchars($cell) . "</td>";
            }
            echo "</tr>";
        }

        echo "</table><br>";
    } else {
        // Message if no data or table doesn't exist
        echo "No data found or table does not exist.<br><br>";
    }
}

// Close database connection
$conn->close();
?>
