<?php

$conn = new mysqli("localhost", "root", "", "Register");

$message = "";
$result = null;

if ($conn->connect_error) {
    die("❌❎ connection failed: " . $conn->connect_error);
} else {
    $message = "✔✅ Database connected successful🥰🎉";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $phone    = trim($_POST["phone"]);
    $password = $_POST["password"];
    $confirm  = $_POST["cpassword"];

    if ($password !== $confirm) {
        $message = "❌ Passwords do not match!";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO register (Name, Email, Phone, Password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $phone, $hashedPassword);

        if ($stmt->execute()) {
            $message = "✅ Registered successfully!";
        } else {
            $message = "❌ Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$result = $conn->query("SELECT id, Name, Email, Phone FROM register ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Table</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            padding: 20px;
        }

        h2 {
            text-align: center;
        }

        .message-box {
            max-width: 90%;
            margin: 20px auto;
            padding: 15px;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
            font-size: 16px;
        }

        .success-box {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error-box {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .back-button {
            text-align: center;
            margin: 20px 0;
        }

        .back-button a {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }

        .back-button a:hover {
            background-color: #0056b3;
        }

        table {
            margin: auto;
            width: 90%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 0 15px rgba(0, 128, 0, 0.2);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #222;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        a {
            padding: 5px 10px;
            text-decoration: none;
            font-weight: bold;
            border-radius: 3px;
        }

        .edit {
            background-color: gold;
            color: black;
        }

        .delete {
            background-color: crimson;
            color: white;
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
    @media (max-width: 640px) {
      body {
        padding: 15px 10px;
      }

      table {
        width: 100% !important;
        font-size: 14px;
      }

      th, td {
        padding: 8px;
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
        background: transparent;
        padding: 0;
        box-shadow: none;
        display: block !important; /* Always show submenu on mobile */
      }

      nav a {
        padding: 10px 5px;
        font-size: 1.1em;
      }

      header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
      }

      header h1 {
        font-size: 1.4em;
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

<h2>📋 All Registered Users</h2>

<!-- 🔙 Back Button -->
<div class="back-button">
    <a href="dashboard.php">🔙 Back to Dashboard</a>
</div>

<?php if ($message): ?>
    <div class="message-box <?= strpos($message, '❌') === 0 ? 'error-box' : 'success-box' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<table>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Phone</th>
    </tr>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= (int)$row['id'] ?></td>
            <td><?= htmlspecialchars($row['Name']) ?></td>
            <td><?= htmlspecialchars($row['Email']) ?></td>
            <td><?= htmlspecialchars($row['Phone']) ?></td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="4" style="text-align:center;">No users found.</td>
        </tr>
    <?php endif; ?>
</table>

</body>
</html>

<?php
if ($result) $result->free();
$conn->close();
?>
