<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "register");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $site_name = $_POST['site_name'];
    $description = $_POST['description'];
    $contact_email = $_POST['contact_email'];
    $contact_phone = $_POST['contact_phone'];
    $contact_address = $_POST['contact_address'];
    $facebook = $_POST['facebook'];
    $twitter = $_POST['twitter'];
    $instagram = $_POST['instagram'];

    $stmt = $conn->prepare("UPDATE site_settings SET site_name=?, description=?, contact_email=?, contact_phone=?, contact_address=?, facebook=?, twitter=?, instagram=? WHERE id=1");
    if ($stmt) {
        $stmt->bind_param("ssssssss", $site_name, $description, $contact_email, $contact_phone, $contact_address, $facebook, $twitter, $instagram);
        if ($stmt->execute()) {
            $message = "✅ Settings updated successfully!";
        } else {
            $message = "❌ Failed to update settings.";
        }
        $stmt->close();
    } else {
        $message = "❌ SQL error: Check your table structure or field names.";
    }
}

$result = $conn->query("SELECT * FROM site_settings WHERE id=1");
$settings = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Site Settings</title>

    <!-- Add this meta tag for mobile responsiveness -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            padding: 40px;
        }

        .container {
            width: 600px;
            background-color: #fff;
            padding: 30px;
            margin: auto;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            border-radius: 10px;
        }

        /* Responsive adjustment: full width on small screens */
        @media (max-width: 650px) {
            body {
                padding: 15px;
            }
            .container {
                width: 100%;
                padding: 15px;
                box-shadow: none;
                border-radius: 0;
            }
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #0072ff;
        }

        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            box-sizing: border-box; /* Ensure padding doesn't break width */
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #0072ff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056cc;
        }

        .message {
            padding: 10px;
            background-color: #e6ffe6;
            color: green;
            border: 1px solid green;
            margin-bottom: 20px;
            border-radius: 6px;
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

    <div class="container">
        <h2>⚙️ TNG Site Settings</h2>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="site_name" value="<?= htmlspecialchars($settings['site_name']) ?>" placeholder="Site Name" required>
            <textarea name="description" placeholder="Description"><?= htmlspecialchars($settings['description']) ?></textarea>

            <input type="email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email']) ?>" placeholder="Contact Email" required>
            <input type="text" name="contact_phone" value="<?= htmlspecialchars($settings['contact_phone']) ?>" placeholder="Contact Phone" required>
            <input type="text" name="contact_address" value="<?= htmlspecialchars($settings['contact_address']) ?>" placeholder="Contact Address" required>

            <input type="text" name="facebook" value="<?= htmlspecialchars($settings['facebook']) ?>" placeholder="Facebook URL">
            <input type="text" name="twitter" value="<?= htmlspecialchars($settings['twitter']) ?>" placeholder="Twitter URL">
            <input type="text" name="instagram" value="<?= htmlspecialchars($settings['instagram']) ?>" placeholder="Instagram URL">

            <button type="submit">💾 Update Settings</button>
        </form>
    </div>

</body>
</html>
