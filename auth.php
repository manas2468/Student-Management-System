<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('Invalid email.');
    }

    $stmt = $conn->prepare("SELECT ID, Username, Password FROM `user` WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows !== 1) {
        echo "User not found. <a href='register.php'>Register</a>";
        exit;
    }
    $stmt->bind_result($id, $username, $hash);
    $stmt->fetch();

    if (password_verify($password, $hash)) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Invalid password. <a href='login.php'>Try again</a>";
    }
    $stmt->close();
}
$conn->close();
?>
