<?php
session_start();

// Only allow admin
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "Register");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'], $_POST['message_id'])) {
    $message_id = intval($_POST['message_id']);
    $reply = $conn->real_escape_string(trim($_POST['reply']));

    // Get user_id from messages table
    $res = $conn->query("SELECT user_id FROM messages WHERE id = $message_id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $user_id = intval($row['user_id']);

        // Get user email
        $user_res = $conn->query("SELECT Email FROM register WHERE id = $user_id");
        if ($user_res && $user_res->num_rows > 0) {
            $user_row = $user_res->fetch_assoc();
            $user_email = $user_row['Email'];

            // Update message with reply and replied_at timestamp
            $update_sql = "UPDATE messages SET reply='$reply', replied_at=NOW() WHERE id = $message_id";
            if ($conn->query($update_sql)) {
                // Send email notification
                $subject = "Reply from TNG Support";
                $body = "Hello,\n\nYou have a new reply from TNG Support:\n\n$reply\n\nRegards,\nTNG Team";
                $headers = "From: support@yourdomain.com\r\nReply-To: support@yourdomain.com\r\n";

                if (mail($user_email, $subject, $body, $headers)) {
                    $success = "Reply sent and saved.";
                } else {
                    $error = "Reply saved but failed to send email.";
                }
            } else {
                $error = "Failed to save reply.";
            }
        } else {
            $error = "User email not found.";
        }
    } else {
        $error = "Message not found.";
    }
}

// Fetch messages with user info
$messages_result = $conn->query("
    SELECT m.id, m.user_id, m.message, m.reply, m.created_at, m.replied_at, r.Name, r.Email 
    FROM messages m 
    LEFT JOIN register r ON m.user_id = r.id 
    ORDER BY m.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Messages - Reply</title>
<style>
    body { font-family: Arial, sans-serif; background:#f0f0f0; margin:0; padding:0; }
    .container { margin-left: 220px; padding: 20px; }
    table { width: 100%; border-collapse: collapse; background: white; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; }
    th { background: #333; color: white; }
    form { margin: 0; }
    textarea { width: 100%; height: 60px; }
    button { padding: 6px 12px; cursor: pointer; }
    .success { color: green; }
    .error { color: red; }
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

<div class="container">
    <h1>Admin - User Messages and Replies</h1>

    <?php if (isset($success)): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Message</th>
                <th>Reply</th>
                <th>Sent At</th>
                <th>Reply At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($msg = $messages_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($msg['Name'] ?? 'Unknown') ?></td>
                <td><?= htmlspecialchars($msg['Email'] ?? 'Unknown') ?></td>
                <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
                <td><?= nl2br(htmlspecialchars($msg['reply'] ?? '')) ?></td>
                <td><?= $msg['created_at'] ?></td>
                <td><?= $msg['replied_at'] ?? '-' ?></td>
                <td>
                    <form method="post" onsubmit="return confirm('Send reply?');">
                        <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                        <textarea name="reply" placeholder="Write reply here..." required><?= htmlspecialchars($msg['reply'] ?? '') ?></textarea><br>
                        <button type="submit">Send Reply</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
