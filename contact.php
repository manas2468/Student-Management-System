<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Contact</title>
  <link rel="stylesheet" href="assets.css">
</head>
<body>
  <header><div class="brand">MySite</div><nav><a href="index.php">Home</a></nav></header>
  <div class="container">
    <h2>Contact Us</h2>
    <form action="save_contact.php" method="POST">
      <label>Your name</label>
      <input name="name" required />
      <label>Email</label>
      <input type="email" name="email" required />
      <label>Message</label>
      <textarea name="message" rows="6" required></textarea>
      <button class="btn" type="submit">Send</button>
    </form>
  </div>
</body>
</html>
