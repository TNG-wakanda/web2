<?php
// register.php

session_start(); // We will use session to send a message to next page

$message = "";

// ✅ Connect to the database
$conn = new mysqli("localhost", "root", "", "Register");

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// ✅ When form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["name"];
    $email    = $_POST["email"];
    $phone    = $_POST["phone"];
    $password = $_POST["password"];
    $confirm  = $_POST["cpassword"];

    // ✅ Check passwords
    if ($password !== $confirm) {
        $message = "❌ Passwords do not match!";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // ✅ Save into database
        $stmt = $conn->prepare("INSERT INTO register (Name, Email, Phone, Password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $phone, $hashedPassword);

        if ($stmt->execute()) {
            $_SESSION["success"] = "✅ Registered successfully!";
            header("Location: success.php");
            exit();
        } else {
            $message = "❌ Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body style="background-color: lightblue; font-family: Arial;">
    <div style="width: 400px; margin: 80px auto; padding: 20px; background: white; border-radius: 10px;">
        <h2>Register</h2>

        <?php if ($message != ""): ?>
            <p style="color:red;"><?= $message ?></p>
        <?php endif; ?>

        <form method="post" action="success.php">
            <input type="text" name="name" placeholder="Username" required><br><br>
            <input type="email" name="email" placeholder="Email" required><br><br>
            <input type="tel" name="phone" placeholder="Phone Number" required><br><br>
            <input type="password" name="password" placeholder="Password" required><br><br>
            <input type="password" name="cpassword" placeholder="Confirm Password" required><br><br>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
