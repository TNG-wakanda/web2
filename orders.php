<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "vubavuba");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION['user']['id'];
$sql = "SELECT o.id, o.order_date, o.status, SUM(oi.quantity * oi.price) AS total
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = ?
        GROUP BY o.id, o.order_date, o.status
        ORDER BY o.order_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Orders</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f0f0f0;
      margin: 0;
      padding: 20px;
    }
    h1 {
      text-align: center;
      color: #333;
    }
    table {
      width: 90%;
      margin: 20px auto;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 15px;
      text-align: center;
      border-bottom: 1px solid #ccc;
    }
    th {
      background-color: #3D63F1;
      color: white;
    }
    tr:hover {
      background-color: #f9f9f9;
    }
    a.back {
      display: block;
      width: 150px;
      margin: 30px auto;
      padding: 10px;
      text-align: center;
      background: #3D63F1;
      color: white;
      text-decoration: none;
      border-radius: 25px;
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

    /* Responsive adjustments */
    @media (max-width: 600px) {
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
        text-align: right;
        padding-left: 50%;
        position: relative;
      }
      td::before {
        content: attr(data-label);
        position: absolute;
        left: 15px;
        width: 45%;
        padding-left: 15px;
        font-weight: bold;
        text-align: left;
      }
      a.back {
        width: 90%;
        font-size: 1.2em;
      }
      nav ul {
        flex-direction: column;
        gap: 10px;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1><img src="img/logo2.jpg" alt="Logo" />TNG</h1>
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

  <h1>My Orders</h1>

  <table>
    <thead>
      <tr>
        <th>Order ID</th>
        <th>Date</th>
        <th>Status</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td data-label="Order ID"><?= htmlspecialchars($row['id']) ?></td>
        <td data-label="Date"><?= htmlspecialchars($row['order_date']) ?></td>
        <td data-label="Status"><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
        <td data-label="Total">$<?= number_format($row['total'], 2) ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <a class="back" href="user_dashboard.php">⬅ Back to Dashboard</a>
</body>
</html>
