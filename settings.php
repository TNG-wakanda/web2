<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$message = "";

$conn = new mysqli("localhost", "root", "", "Register");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $photoPath = $user['Photo'] ?? '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) mkdir($targetDir);
            $photoPath = $targetDir . basename($_FILES['photo']['name']);
            move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath);
        }

        $stmt = $conn->prepare("UPDATE register SET Name=?, Email=?, Phone=?, Photo=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $email, $phone, $photoPath, $user['id']);
        if ($stmt->execute()) {
            $_SESSION['user']['Name'] = $name;
            $_SESSION['user']['Email'] = $email;
            $_SESSION['user']['Phone'] = $phone;
            $_SESSION['user']['Photo'] = $photoPath;
            $message = "✅ Profile updated successfully!";
        } else {
            $message = "❌ Failed to update profile.";
        }
    }

    if (isset($_POST['change_password'])) {
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        if ($new_pass !== $confirm_pass) {
            $message = "❌ Passwords do not match!";
        } else {
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE register SET Password=? WHERE id=?");
            $stmt->bind_param("si", $hashed, $user['id']);
            if ($stmt->execute()) {
                $message = "✅ Password changed successfully!";
            } else {
                $message = "❌ Failed to change password.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Settings</title>

    <!-- Mobile viewport for responsiveness -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 40px;
        }

        .settings-box {
            max-width: 600px;
            background: white;
            margin: auto;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            border-radius: 10px;
        }

        /* Responsive tweaks */
        @media (max-width: 650px) {
            body {
                padding: 15px;
            }
            .settings-box {
                width: 100%;
                padding: 15px;
                box-shadow: none;
                border-radius: 0;
            }
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
        }

        input[type="text"], input[type="email"], input[type="password"], input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box; /* Prevent input overflow */
        }

        button {
            width: 100%;
            padding: 12px;
            background: #0072ff;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background: #0056cc;
        }

        .message {
            text-align: center;
            color: green;
            margin-bottom: 15px;
        }

        .section {
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 30px;
        }

        .user-photo {
            text-align: center;
            margin-bottom: 20px;
        }

        .user-photo img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
        }

        header {
          background-color: white;
          color: black;
          padding: 20px;
          display: flex;
          justify-content: space-between;
          align-items: center;
          width: 100%;
          position: sticky;
          top: 0;
          z-index: 999;
        }

        header h1 {
          margin: 0;
          display: flex;
          align-items: center;
        }

        header img {
          width: 50px;
          height: 30px;
          margin-right: 10px;
          border-radius: 15px;
        }

        nav ul {
          list-style: none;
          margin: 0;
          padding: 0;
          display: flex;
          gap: 20px;
        }

        nav li {
          position: relative;
        }

        nav a {
          text-decoration: none;
          color: black;
          padding: 7px 15px;
          display: block;
          font-size: large;
          font-weight: bolder;
        }

        nav a:hover {
          color: blue;
        }

        .submenu {
          display: none;
          position: absolute;
          top: 100%;
          left: 0;
          background: gainsboro;
          padding: 10px;
          border-radius: 5px;
          box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .submenu a {
          color: black;
          margin: 5px 0;
        }

        nav li:hover .submenu {
          display: block;
        }
    </style>
</head>
<body>
     <header>
        <h1><img src="img/logo2.jpg" alt="Logo">TNG</h1>
        <nav>
          <ul>
            <li><a href="webpage.php">Home</a></li>
            <li>
              <a href="#">Products</a>
              <div class="submenu">
                <a href="product1.php">Product 1</a>
                <a href="product2.php">Product 2</a>
                <a href="product3.php">Product 3</a>
              </div>
            </li>
            <li><a href="contact.php">Contacts</a></li>
          </ul>
        </nav>
    </header>

    <div class="settings-box">
        <h2>⚙️ Settings</h2>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="user-photo">
            <img src="<?= isset($_SESSION['user']['Photo']) ? htmlspecialchars($_SESSION['user']['Photo']) : 'user_default.png' ?>" alt="Profile Picture">
        </div>

        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['Name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['Email']) ?>" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($user['Phone']) ?>" required>
            </div>
            <div class="form-group">
                <label>Profile Picture</label>
                <input type="file" name="photo" accept="image/*">
            </div>
            <button type="submit" name="update_profile">💾 Update Profile</button>
        </form>

        <div class="section">
            <form method="post">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" name="change_password">🔐 Change Password</button>
            </form>
        </div>
    </div>

</body>
</html>
