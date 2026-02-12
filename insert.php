<?php
// Include the database connection and configurations
// Make sure config.php is in the same directory or provide the correct path.
require_once 'config.php'; // Use require_once to ensure config.php is included only once and stops execution if it fails.

$errors = [];
$success = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve input, with default values
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? ''; // Password is not trimmed as it might have leading/trailing spaces that are valid.

    // --- Input Validation ---
    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters long.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    // --- Database Operations (only if no validation errors) ---
    if (empty($errors)) {
        // Hash the password for secure storage
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL statement to prevent SQL injection
        $sql = "INSERT INTO `user` (Email, Password, Username) VALUES (?, ?, ?)";

        // Prepare the statement
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            // Bind parameters to the prepared statement
            // "sss" indicates that all three parameters are strings
            $bindSuccess = $stmt->bind_param("sss", $email, $hashedPassword, $username);

            if ($bindSuccess) {
                // Execute the prepared statement
                if ($stmt->execute()) {
                    $success = "Account created successfully! You can now <a href='login.php'>login here</a>.";
                } else {
                    // Error during execution (e.g., duplicate email if unique constraint exists)
                    // Check for specific error codes if needed (e.g., duplicate entry)
                    // $error_code = $stmt->errno; // e.g., 1062 for duplicate entry
                    $errors[] = "Database error: Failed to create account. Please try again. (" . htmlspecialchars($stmt->error) . ")";
                }
            } else {
                // Error binding parameters
                $errors[] = "Database error: Failed to bind parameters. (" . htmlspecialchars($stmt->error) . ")";
            }

            // Close the statement
            $stmt->close();
        } else {
            // Error preparing the statement (e.g., SQL syntax error, table/column not found)
            $errors[] = "Database error: Could not prepare the statement. (" . htmlspecialchars($conn->error) . ")";
        }
    }
}

// Close the database connection
// Important: Close connection *after* all operations are done.
// If config.php uses 'die()' on connection failure, this part might not be reached, which is fine.
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Result</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5; /* Slightly softer background */
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Use min-height for better mobile responsiveness */
            color: #333;
        }
        .box {
            background: #fff;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); /* Slightly more pronounced shadow */
            max-width: 450px;
            width: 90%; /* Responsive width */
            text-align: center;
            animation: fadeIn 0.7s cubic-bezier(0.25, 0.8, 0.25, 1); /* Smoother animation */
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }
        h1, h2 {
            color: #2c3e50; /* Darker heading color */
            margin-bottom: 20px;
        }
        .error {
            color: #c0392b; /* Red error color */
            background-color: #fdecea; /* Light red background for errors */
            border: 1px solid #e74c3c; /* Red border */
            padding: 12px 15px;
            margin-bottom: 15px;
            border-radius: 6px;
            font-weight: 500;
            text-align: left; /* Align error messages to the left */
            box-sizing: border-box; /* Include padding in width calculation */
        }
        .success {
            color: #27ae60; /* Green success color */
            background-color: #e8f8ef; /* Light green background for success */
            border: 1px solid #2ecc71; /* Green border */
            padding: 12px 15px;
            margin-bottom: 15px;
            border-radius: 6px;
            font-weight: 500;
            text-align: left; /* Align success message to the left */
            box-sizing: border-box; /* Include padding in width calculation */
        }
        a.btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px; /* Slightly larger button */
            background: #3498db; /* Nice blue */
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s ease, transform 0.2s ease;
            border: none; /* Remove default button border */
            cursor: pointer; /* Indicate clickable */
        }
        a.btn:hover {
            background: #2980b9; /* Darker blue on hover */
            transform: translateY(-2px); /* Slight lift on hover */
        }
        .message-container {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="box">
        <?php if (!empty($errors)): ?>
            <h2>Registration Failed ❌</h2>
            <div class="message-container">
                <?php foreach ($errors as $e): ?>
                    <div class="error"><?php echo htmlspecialchars($e); // Ensure output is always escaped ?></div>
                <?php endforeach; ?>
            </div>
            <a href="register.php" class="btn">Try Again</a>
        <?php elseif (!empty($success)): ?>
            <h2>Success! 🎉</h2>
            <div class="message-container">
                <div class="success"><?php echo $success; // Success message already contains an <a> tag ?></div>
            </div>
            <!-- No explicit back button here as the success message has a login link -->
        <?php else: ?>
            <h2>No Activity</h2>
            <p>No form data was submitted or processed.</p>
            <a href="register.php" class="btn">Go to Registration</a>
        <?php endif; ?>
    </div>
</body>
</html>