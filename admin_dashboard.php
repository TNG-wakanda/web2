<?php
session_start();

// Redirect if not logged in or not admin
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "Register");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch users for management
$result = $conn->query("SELECT * FROM register");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .sidebar {
            width: 200px;
            height: 100vh;
            background: #333;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 50px;
        }
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: #555;
        }
        .main {
            margin-left: 200px;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #222;
            color: white;
        }
        a.btn {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .edit {
            background-color: orange;
            color: white;
        }
        .delete {
            background-color: red;
            color: white;
        }
        .logout {
            background-color: #444;
            position: absolute;
            bottom: 20px;
            width: 100%;
            text-align: center;
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

<div class="sidebar">
    <h2 style="text-align:center;">Admin</h2>
    <a href="admin_dashboard.php">📋 User Management</a>
    <a href="create_user.php">➕ Add User</a>
    <a href="site_settings.php">⚙️ Site Settings</a>
    <a href="messages.php">📩 View Messages</a>
    <a href="reports.php">📈 Reports</a>
    <a href="logout.php" class="logout">🚪 Logout</a>
</div>

<div class="main">
    <h1>Admin Dashboard - User Management</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['Name']) ?></td>
            <td><?= htmlspecialchars($row['Email']) ?></td>
            <td><?= htmlspecialchars($row['Phone']) ?></td>
            <td><?= htmlspecialchars($row['Role']) ?></td>
            <td>
                <a class="btn edit" href="edit.php?id=<?= urlencode($row['id']) ?>">Edit</a>
                <a class="btn delete" href="delete.php?id=<?= urlencode($row['id']) ?>" onclick="return confirm('Delete this user?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
