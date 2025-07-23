<?php
session_start();
$message = "";

$conn = new mysqli("localhost", "root", "", "register");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM register WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['Password'])) {
            $_SESSION['user'] = $user;

            if ($user['Role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            $message = "❌ Incorrect password!";
        }
    } else {
        $message = "❌ Email not found!";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - TNG</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #1f1c2c, #928dab);
            color: #fff;
            min-height: 100vh;
        }

        header {
            background-color: #fff;
            color: black;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        header h1 {
            display: flex;
            align-items: center;
            font-size: 1.5em;
        }

        header img {
            width: 50px;
            height: 30px;
            margin-right: 10px;
            border-radius: 5px;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 25px;
        }

        nav a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }

        nav a:hover {
            color: #007bff;
        }

        .submenu {
            display: none;
            position: absolute;
            top: 40px;
            left: 0;
            background: #eee;
            padding: 10px;
            border-radius: 5px;
        }

        nav li:hover .submenu {
            display: block;
        }

        .login-box {
            background: #fff;
            color: #000;
            padding: 40px;
            border-radius: 12px;
            width: 350px;
            margin: 80px auto;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }

        .login-box h2 {
            margin-bottom: 20px;
            font-size: 22px;
            font-weight: bold;
        }

        .login-box input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 15px;
        }

        .login-box button {
            background-color: #1f1c2c;
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-box button:hover {
            background-color: #3a3455;
        }

        .message {
            color: red;
            margin-top: 10px;
            font-weight: bold;
        }

        .login-box a {
            text-decoration: none;
            color: #007bff;
            display: block;
            text-align: center;
            margin-top: 12px;
        }

        @media (max-width: 400px) {
            .login-box {
                width: 90%;
                padding: 20px;
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

<div class="login-box">
    <h2>Login to TNG</h2>

    <?php if (!empty($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login Now</button>
    </form>

    <a href="register.php">Don't have an account? Register here</a>
</div>

</body>
</html>
