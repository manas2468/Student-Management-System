<?php
include 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $message = trim($_POST['message'] ?? '');

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Invalid email.');
  }

  // create contacts table if not exists (safe)
  $conn->query("CREATE TABLE IF NOT EXISTS contacts (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200),
    email VARCHAR(255),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $name, $email, $message);
  if ($stmt->execute()) {
    echo "Message saved. <a href='index.php'>Home</a>";
  } else {
    echo "Error: " . htmlspecialchars($stmt->error);
  }
  $stmt->close();
}
$conn->close();
?>
