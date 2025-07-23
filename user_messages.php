<?php
session_start();

// Check if user is logged in and has role 'user'
if (!isset($_SESSION['user']) || strtolower($_SESSION['user']['role'] ?? '') !== 'user') {
    header("Location: login.php");
    exit();
}

$userEmail = $_SESSION['user']['email'] ?? '';

// Connect to database
$conn = new mysqli("localhost", "root", "", "register");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";

// Handle new message POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_message'])) {
    $newMsg = trim($_POST['new_message']);
    if ($newMsg !== "") {
        $stmt = $conn->prepare("INSERT INTO messages (email, message, created_at) VALUES (?, ?, NOW())");
        if ($stmt) {
            $stmt->bind_param("ss", $userEmail, $newMsg);
            if ($stmt->execute()) {
                $message = "✅ Message sent!";
            } else {
                $message = "❌ Failed to send message.";
            }
            $stmt->close();
        } else {
            $message = "❌ Failed to prepare statement.";
        }
    } else {
        $message = "⚠️ Message cannot be empty.";
    }
}

// Fetch user's messages
$messages = [];
$stmt = $conn->prepare("SELECT message, reply, created_at, replied_at FROM messages WHERE email = ? ORDER BY created_at ASC");
if ($stmt) {
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Messages - TNG</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        h2 { text-align: center; }
        .chat-box { max-height: 400px; overflow-y: auto; margin-top: 20px; border: 1px solid #ddd; padding: 10px; border-radius: 5px; background: #fdfdfd; }
        .msg { margin: 10px 0; padding: 10px; border-radius: 10px; max-width: 80%; }
        .user-msg { background-color: #dcf8c6; text-align: right; margin-left: auto; }
        .admin-msg { background-color: #eee; text-align: left; margin-right: auto; }
        .form-group { margin-top: 20px; display: flex; gap: 10px; }
        input[type=text] {
            flex-grow: 1;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        input[type=submit] {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            white-space: nowrap;
        }
        input[type=submit]:hover { background-color: #0056b3; }
        .message-box { margin-bottom: 10px; }
        .timestamp { font-size: 0.8em; color: gray; margin-top: 4px; }
        .flash { color: green; margin-bottom: 10px; text-align: center; font-weight: bold; }

        /* Header & Nav styles */
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
          box-shadow: 0 2px 5px rgba(0,0,0,0.1);
          box-sizing: border-box;
        }

        header h1 {
          margin: 0;
          display: flex;
          align-items: center;
          font-size: 24px;
          font-weight: bold;
        }

        header img {
          width: 50px;
          height: 30px;
          margin-right: 10px;
          border-radius: 15px;
          object-fit: cover;
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
          z-index: 1000;
        }

        .submenu a {
          color: black;
          margin: 5px 0;
          white-space: nowrap;
        }

        nav li:hover .submenu {
          display: block;
        }
    </style>
</head>
<body>
    <header>
        <h1><img src="img/logo2.jpg" alt="TNG Logo">TNG</h1>
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

    <div class="container">
        <h2>📨 My Messages</h2>

        <?php if ($message): ?>
            <p class="flash"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <div class="chat-box" aria-live="polite" aria-label="Chat messages">
            <?php if (empty($messages)): ?>
                <p>No messages yet. Start a conversation!</p>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="msg user-msg">
                        <div><strong>You:</strong> <?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                        <div class="timestamp"><?= htmlspecialchars($msg['created_at']) ?></div>
                    </div>
                    <?php if (!empty($msg['reply'])): ?>
                        <div class="msg admin-msg">
                            <div><strong>Admin:</strong> <?= nl2br(htmlspecialchars($msg['reply'])) ?></div>
                            <div class="timestamp"><?= htmlspecialchars($msg['replied_at'] ?? 'Pending') ?></div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <form method="POST" class="form-group" aria-label="Send a new message">
            <input type="text" name="new_message" placeholder="Type your message here..." required autocomplete="off" aria-required="true" />
            <input type="submit" value="Send Message" />
        </form>
    </div>
</body>
</html>
