<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "register");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$sql = "SELECT * FROM messages ORDER BY created_at DESC";
$result = $conn->query($sql);
if (!$result) {
    die("SQL Error: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Messages - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #fafafa; }
        table { border-collapse: collapse; width: 100%; background: white; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; vertical-align: top; }
        th { background-color: #f2f2f2; }
        tr.unread { background-color: #e8f0fe; font-weight: bold; }
        /* Styled reply button */
        .reply-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 20px 0;
            transition: background-color 0.3s ease;
        }
        .reply-button:hover {
            background-color: #0056b3;
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
          box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        header h1 {
          margin: 0;
          display: flex;
          align-items: center;
          font-size: 1.5em;
          gap: 10px;
        }

        header img {
          width: 50px;
          height: 30px;
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
        /* Responsive table for small devices */
        @media (max-width: 700px) {
          table, thead, tbody, th, td, tr {
            display: block;
          }
          thead tr {
            display: none;
          }
          tr {
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 10px;
            background: white;
          }
          td {
            padding-left: 50%;
            position: relative;
            text-align: left;
          }
          td::before {
            position: absolute;
            left: 10px;
            width: 45%;
            padding-left: 10px;
            font-weight: bold;
            white-space: nowrap;
          }
          td:nth-of-type(1)::before { content: "ID"; }
          td:nth-of-type(2)::before { content: "User ID"; }
          td:nth-of-type(3)::before { content: "Message"; }
          td:nth-of-type(4)::before { content: "Reply"; }
          td:nth-of-type(5)::before { content: "Sent At"; }
          td:nth-of-type(6)::before { content: "Replied At"; }
          td:nth-of-type(7)::before { content: "Status"; }
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

<h2>📩 Messages</h2>

<table>
    <tr>
        <th>ID</th>
        <th>User ID</th>
        <th>Message</th>
        <th>Reply</th>
        <th>Sent At</th>
        <th>Replied At</th>
        <th>Status</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr class="<?= $row['is_read'] ? '' : 'unread' ?>">
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['user_id']) ?></td>
            <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
            <td><?= nl2br(htmlspecialchars($row['reply'])) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
            <td><?= $row['replied_at'] ? htmlspecialchars($row['replied_at']) : '—' ?></td>
            <td><?= $row['is_read'] ? '✅ Read' : '📩 Unread' ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<a href="admin_reply.php" class="reply-button">Reply</a>

</body>
</html>
