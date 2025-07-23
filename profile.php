<?php
session_start();
$conn = new mysqli("localhost", "root", "", "Register");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST['phone']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Invalid email format.";
    } else {
        $stmt = $conn->prepare("UPDATE register SET Name=?, Email=?, Phone=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $phone, $user_id);

        if ($stmt->execute()) {
            $message = "✅ Profile updated successfully.";
            $_SESSION['user']['Name'] = $name;
            $_SESSION['user']['Email'] = $email;
            $_SESSION['user']['Phone'] = $phone;
        } else {
            $message = "❌ Failed to update profile.";
        }
        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT * FROM register WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile - TNG</title>
    <style>
        body {
            background: #f0f0f0;
            font-family: Arial, sans-serif;
            padding: 20px;
            margin: 0;
        }
        .profile-box {
            background: white;
            max-width: 500px;
            margin: 30px auto;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px gray;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            margin-top: 15px;
            display: block;
        }
        input[type=text], input[type=email] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 15px;
        }
        button {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            background: #0072ff;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056cc;
        }
        .message {
            margin-top: 20px;
            font-weight: bold;
            text-align: center;
            color: green;
        }
        .message.error {
            color: red;
        }
        /* Header & nav styles */
        header {
            background-color: white;
            color: black;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 1px 5px rgba(0,0,0,0.1);
        }
        header h1 {
            margin: 0;
            display: flex;
            align-items: center;
            font-size: 24px;
            font-weight: bolder;
            letter-spacing: 1px;
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
            font-size: large;
            font-weight: bolder;
            display: block;
        }
        nav a:hover {
            color: #0072ff;
        }
        .submenu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: gainsboro;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        .submenu a {
            color: black;
            margin: 5px 0;
            font-weight: normal;
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

<div class="profile-box">
    <h2>👤 My Profile</h2>

    <?php if ($message): ?>
        <div class="message <?= strpos($message, '❌') === 0 ? 'error' : '' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="name">Name</label>
        <input id="name" type="text" name="name" value="<?= htmlspecialchars($user['Name']) ?>" required>

        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="<?= htmlspecialchars($user['Email']) ?>" required>

        <label for="phone">Phone</label>
        <input id="phone" type="text" name="phone" value="<?= htmlspecialchars($user['Phone']) ?>" required>

        <button type="submit">Update Profile</button>
    </form>
</div>
</body>
</html>
