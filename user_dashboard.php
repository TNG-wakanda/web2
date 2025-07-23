<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$message = "";

$conn = new mysqli("localhost", "root", "", "Register");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Get submitted form values safely
        $name  = trim($_POST['name'] ?? $user['Name']);
        $email = trim($_POST['email'] ?? $user['Email']);
        $phone = trim($_POST['phone'] ?? $user['Phone']);
        $address = trim($_POST['address'] ?? $user['Address']);
        
        // Handle photo upload
        $photoPath = $user['Photo'] ?? '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
            $fileName = basename($_FILES['photo']['name']);
            $photoPath = $targetDir . time() . "_" . preg_replace('/[^a-zA-Z0-9_\.-]/', '', $fileName);
            move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath);
        }

        // Update DB
        $stmt = $conn->prepare("UPDATE register SET Name=?, Email=?, Phone=?, Address=?, Photo=? WHERE id=?");
        $stmt->bind_param("sssssi", $name, $email, $phone, $address, $photoPath, $user['id']);
        if ($stmt->execute()) {
            // Update session data to reflect changes
            $_SESSION['user']['Name'] = $name;
            $_SESSION['user']['Email'] = $email;
            $_SESSION['user']['Phone'] = $phone;
            $_SESSION['user']['Address'] = $address;
            $_SESSION['user']['Photo'] = $photoPath;
            $message = "✅ Profile updated successfully!";
        } else {
            $message = "❌ Failed to update profile: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<!-- (Keep your existing head content here) -->
</head>
<body>
  <header>
    <!-- (Keep your existing header/navigation here, but remove empty nav item) -->
    <h1><img src="img/logo2.jpg" alt="Logo">TNG</h1>
    <nav>
      <ul>
        <li><a href="webpage.php">Home</a></li>
        <li><a href="settings.php">Settings</a></li>
        <li><a href="chat.php">Chat✨Support</a></li>
      </ul>
    </nav>
  </header>

  <div class="container">
    <div class="user-info">
       <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
       <?php endif; ?>

       <div class="user-photo">
          <img src="<?= htmlspecialchars($_SESSION['user']['Photo'] ?? 'user_default.png') ?>" alt="Profile Picture">
       </div>

       <h2><?= htmlspecialchars($_SESSION['user']['Name']) ?></h2>
       <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user']['Email']) ?></p>
       <p><strong>Phone:</strong> <?= htmlspecialchars($_SESSION['user']['Phone']) ?></p>
       <p><strong>Address:</strong> <?= htmlspecialchars($_SESSION['user']['Address']) ?></p>

       <div class="actions">
         <a href="settings.php">Update Profile</a>
         <a href="user_messages.php">My Messages</a>
         <a href="orders.php">My Orders</a>
       </div>

       <div class="logout">
         <a href="logout.php">🚪 Logout</a>
       </div>
    </div>
  </div>
</body>
</html>
