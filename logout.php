<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logging Out</title>
    <script>
        // Redirect to index.php after 1 second
        setTimeout(function() {
            window.location.href = 'index.php';
        }, 1000);
    </script>
</head>
<body>
    <p>Logging you out... Redirecting to home page.</p>
</body>
</html>
