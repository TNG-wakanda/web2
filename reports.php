<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "register");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$total_users = $conn->query("SELECT COUNT(*) AS total FROM register")->fetch_assoc()['total'];
$admins = $conn->query("SELECT COUNT(*) AS total FROM register WHERE Role = 'admin'")->fetch_assoc()['total'];
$messages = $conn->query("SELECT COUNT(*) AS total FROM messages")->fetch_assoc()['total'];

// Calculate regular users
$regular_users = $total_users - $admins;

// Auto chat summary
if ($messages > 0) {
    $chat_summary = "💬 There are <strong>$messages messages</strong> shared among <strong>$total_users users</strong>.";
} else {
    $chat_summary = "😶 No messages yet. Encourage users to start a conversation!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>📊 Admin Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 40px;
        }
        .report-card {
            background: white;
            padding: 20px;
            margin: 20px auto;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        h2 { text-align: center; color: #333; }
        ul { list-style: none; padding: 0; }
        li {
            background: #f9f9f9;
            padding: 12px;
            margin-bottom: 10px;
            border-left: 5px solid #007bff;
            font-size: 16px;
        }
        .summary {
            background: #e9f7ef;
            border-left: 5px solid #28a745;
            padding: 15px;
            margin-top: 25px;
            font-size: 16px;
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


    /* Responsive for mobile devices */
    @media (max-width: 640px) {
      body {
        padding: 20px 10px;
      }

      .report-card {
        width: 100% !important;
        margin: 10px auto;
        padding: 20px 15px;
      }

      nav ul {
        flex-direction: column;
        gap: 10px;
      }

      nav li {
        position: static;
      }

      .submenu {
        position: static;
        box-shadow: none;
        background: transparent;
        padding: 0;
        display: block !important; /* always visible on mobile */
      }

      nav a {
        padding: 10px 5px;
        font-size: 1.1em;
      }

      header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
      }

      header h1 {
        font-size: 1.5em;
      }

      header img {
        width: 40px;
        height: 25px;
      }
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
    <div class="report-card">
        <h2>📊 System Report Overview</h2>
        <ul>
            <li>Total Registered Users: <strong><?= $total_users ?></strong></li>
            <li>Total Admins: <strong><?= $admins ?></strong></li>
            <li>Total Regular Users: <strong><?= $regular_users ?></strong></li>
            <li>Total Messages: <strong><?= $messages ?></strong></li>
        </ul>
        <div class="summary">
            <?= $chat_summary ?>
        </div>
    </div>
</body>
</html>
