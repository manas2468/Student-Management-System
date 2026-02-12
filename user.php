<?php
include 'config.php';
$result = $conn->query("SELECT Username, Email, Created_at FROM `user` ORDER BY Created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>All Users</title>
  <link rel="stylesheet" href="assets.css">
</head>
<body>
  <header><div class="brand">MySite</div><nav><a href="index.php">Home</a></nav></header>
  <div class="container">
    <h2>Registered Users</h2>
    <table class="table">
      <tr><th>Username</th><th>Email</th><th>Joined</th></tr>
      <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($row['Username']); ?></td>
          <td><?php echo htmlspecialchars($row['Email']); ?></td>
          <td><?php echo htmlspecialchars($row['Created_at']); ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</body>
</html>
