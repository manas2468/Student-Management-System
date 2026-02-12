<?php
// --- DEBUGGING: Uncomment these lines to see ALL errors ---
// This is crucial for diagnosing the "white screen" issue.
// REMEMBER TO REMOVE THESE LINES WHEN DEPLOYING TO A LIVE SERVER.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- END DEBUGGING ---

// Database credentials
// For InfinityFree, your database username and database name are often the same.
define('DB_SERVER', 'sql302.infinityfree.com');
define('DB_USERNAME', 'if0_39892961'); // Your InfinityFree DB username
define('DB_PASSWORD', 'mdk48825');     // Your InfinityFree DB password
define('DB_NAME', 'if0_39892961_loginapp'); // Your InfinityFree DB name

// Attempt to establish a database connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    // Use htmlspecialchars() for security when displaying error messages
    // 'die()' will stop script execution immediately if the connection fails.
    die("Connection failed: " . htmlspecialchars($conn->connect_error));
}

// Optional: Set the character set to utf8mb4 for better character support
// It's good practice to set this for international characters.
if (!$conn->set_charset("utf8mb4")) {
    // Log the error if setting charset fails, so it doesn't break the script
    // but can be reviewed in server logs.
    error_log("Error loading character set utf8mb4: " . $conn->error);
}

// You can add other configuration settings here if needed.
// For example, if you were using PDO, you might set attributes like this:
// $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// The $conn variable is now ready to be used for database operations.

?>