<?php
// success.php
session_start();
$message = isset($_SESSION["success"]) ? $_SESSION["success"] : "✅ You have reached this page!";
unset($_SESSION["success"]); // Clear the message so it doesn’t show again on refresh
?>

<!DOCTYPE html>
<html>
<head>
    <title>Success</title>
</head>
<body style="background-color: lightgreen; font-family: Arial;">
    <div style="width: 400px; margin: 100px auto; padding: 30px; background: white; border-radius: 10px; text-align: center;">
        <h2><?php echo $message; ?></h2>
        <a href="register.php">← Back to Register</a>
    </div>
</body>
</html>
