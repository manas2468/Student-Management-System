<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user type
$stmt = $conn->prepare("SELECT user_type FROM user WHERE ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$is_admin = ($user['user_type'] === 'admin');
$stmt->close();

if (!$is_admin) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get all students
$stmt = $conn->prepare("SELECT ID, Username, Email FROM user WHERE user_type = 'student' ORDER BY Username ASC");
$stmt->execute();
$result = $stmt->get_result();
$students = [];

while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode(['success' => true, 'students' => $students]);
$stmt->close();
$conn->close();
?>