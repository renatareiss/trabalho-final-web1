<?php
// IMPORTANT: Replace the following values with your actual database credentials and name.
$servername = "localhost"; // Or your DB server address
$username = "root"; // Your DB username
$password = ""; // Your DB password
$dbname = "typing_game"; // Your DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to close the connection (optional, as PHP closes it automatically at script end)
function close_connection($conn) {
    $conn->close();
}
?>
