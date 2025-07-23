<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "Register");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$user = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM register WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if (!$user) {
        die("❌ User not found.");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id    = $_POST['id'];
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    $stmt = $conn->prepare("UPDATE register SET Name=?, Email=?, Phone=? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $phone, $id);
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <style>
        /* Your CSS styles as in your snippet */
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
        form {
            max-width: 400px;
            margin: 20px auto;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 8px 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            background-color: #3D63F1;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #3049c4;
        }
        a {
            display: inline-block;
            margin: 15px 0;
            text-decoration: none;
            color: #3D63F1;
        }
        a:hover {
            text-decoration: underline;
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

<h2 style="text-align:center;">Edit User</h2>

<?php if ($user): ?>
<form method="POST">
    <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
    <label>Name:</label>
    <input type="text" name="name" value="<?= htmlspecialchars($user['Name']) ?>" required>

    <label>Email:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['Email']) ?>" required>

    <label>Phone:</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($user['Phone']) ?>" required>

    <button type="submit">Update</button>
</form>
<?php else: ?>
    <p style="text-align:center; color: red;">⚠️ No user data to edit.</p>
<?php endif; ?>

<p style="text-align:center;"><a href="dashboard.php">🔙 Back to Dashboard</a></p>

</body>
</html>
