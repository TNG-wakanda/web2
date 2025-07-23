<?php
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $cpass    = $_POST['cpassword'];
    $phone    = trim($_POST['phone']);
    $address  = trim($_POST['address']);

    if ($password !== $cpass) {
        $message = "❌ Passwords do not match!";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        // Connect to register DB
        $conn1 = new mysqli("localhost", "root", "", "register");
        if ($conn1->connect_error) {
            die("Connection failed to register DB: " . $conn1->connect_error);
        }
        $conn1->set_charset("utf8mb4");

        // Connect to orders DB
        $conn2 = new mysqli("localhost", "root", "", "orders");
        if ($conn2->connect_error) {
            die("Connection failed to orders DB: " . $conn2->connect_error);
        }
        $conn2->set_charset("utf8mb4");

        // Check if email already exists in register DB
        $checkStmt = $conn1->prepare("SELECT id FROM register WHERE Email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();
        if ($checkStmt->num_rows > 0) {
            $message = "❌ Email already registered!";
            $checkStmt->close();
        } else {
            $checkStmt->close();

            // Insert into register DB
            $stmt1 = $conn1->prepare("INSERT INTO register (Name, Email, Password, Phone, Address) VALUES (?, ?, ?, ?, ?)");
            $stmt1->bind_param("sssss", $name, $email, $hashed, $phone, $address);

            // Insert into orders.users table
            $stmt2 = $conn2->prepare("INSERT INTO users (fullnames, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
            $stmt2->bind_param("sssss", $name, $email, $hashed, $phone, $address);

            $success1 = $stmt1->execute();
            $success2 = $stmt2->execute();

            if ($success1 && $success2) {
                $orders_user_id = $conn2->insert_id;

                $_SESSION['user'] = [
                    'id' => $orders_user_id,
                    'name' => $name,
                    'email' => $email,
                ];

                $message = "✅ Registration successful! You are now logged in.";
            } else {
                $error1 = $stmt1->error ?: '';
                $error2 = $stmt2->error ?: '';
                $message = "❌ Error occurred: Register DB - $error1 | Orders DB - $error2";
            }

            $stmt1->close();
            $stmt2->close();
        }

        $conn1->close();
        $conn2->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>User Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .form-box {
            background: #fff;
            max-width: 400px;
            margin: 40px auto;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            background: #f8d7da;
            color: #721c24;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 500px) {
            .form-box {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>User Registration</h2>

        <?php if (!empty($message)) {
            $class = strpos($message, '✅') === 0 ? 'success' : '';
            echo "<div class='message $class'>$message</div>";
        } ?>

        <form method="POST" action="">
            <label>Full Name</label>
            <input type="text" name="name" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label>Confirm Password</label>
            <input type="password" name="cpassword" required>

            <label>Phone</label>
            <input type="text" name="phone" required>

            <label>Address</label>
            <input type="text" name="address" required>

            <input type="submit" value="Register">
        </form>

        <p class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </div>
</body>
</html>
