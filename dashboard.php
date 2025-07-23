<?php
session_start();

$conn = new mysqli("localhost", "root", "", "Register");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$result = $conn->query("SELECT * FROM register");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Table</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f0f0f0;
        }

        header {
            background-color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid #ddd;
        }

        header h1 {
            display: flex;
            align-items: center;
            margin: 0;
        }

        header img {
            height: 40px;
            margin-right: 10px;
            border-radius: 10px;
        }

        nav ul {
            list-style: none;
            display: flex;
            padding: 0;
            margin: 0;
            gap: 15px;
        }

        nav li {
            position: relative;
        }

        nav a {
            text-decoration: none;
            color: #222;
            font-weight: bold;
            padding: 8px 12px;
        }

        nav a:hover {
            color: blue;
        }

        .submenu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: #eee;
            padding: 10px;
            border-radius: 5px;
            min-width: 150px;
        }

        .submenu a {
            display: block;
            margin: 5px 0;
        }

        nav li:hover .submenu {
            display: block;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
        }

        .sidebar {
            width: 200px;
            background: #222;
            color: white;
            min-height: 100vh;
            padding-top: 20px;
            position: sticky;
            top: 60px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            font-weight: bold;
            border-bottom: 1px solid #444;
        }

        .sidebar a:hover {
            background: #444;
        }

        .main-content {
            flex: 1;
            padding: 20px;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px gray;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background: #222;
            color: white;
        }

        a.edit {
            background-color: gold;
            color: black;
        }

        a.delete {
            background-color: crimson;
            color: white;
        }

        a.edit, a.delete {
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                position: static;
            }

            nav ul {
                flex-direction: column;
                gap: 0;
                background: white;
            }

            nav li {
                border-bottom: 1px solid #ccc;
            }

            header {
                flex-direction: column;
                align-items: flex-start;
            }

            header h1 {
                margin-bottom: 10px;
            }

            .main-content {
                margin-top: 10px;
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

<div class="container">
    <div class="sidebar">
        <a href="admin_dashboard.php">🏠 Home</a>
        <a href="register.php">📝 Register</a>
        <a href="profile.php">👤 Profile</a>
        <a href="dashboard.php">📊 Dashboard</a>
        <a href="settings.php">⚙️ Settings</a>
    </div>

    <div class="main-content">
        <h2>📋 All Registered Users</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
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
                <td><?= htmlspecialchars($row['Role'] ?? 'user') ?></td>
                <td>
                    <a class="edit" href="edit.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to edit this user?');">Edit</a>
                    <a class="delete" href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

</body>
</html>
