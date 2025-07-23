<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    echo "❌ No user ID provided for deletion.";
    exit();
}

$id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // If user confirmed deletion
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        $conn = new mysqli("localhost", "root", "", "register");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("DELETE FROM register WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: registertable.php");
            exit();
        } else {
            echo "❌ Error deleting user: " . htmlspecialchars($conn->error);
        }

        $stmt->close();
        $conn->close();
    } else {
        // User cancelled deletion
        header("Location: registertable.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Delete User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background-color: #f4f4f4;
            text-align: center;
        }
        h2 {
            color: #c00;
        }
        form button {
            padding: 10px 20px;
            margin: 10px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button.confirm {
            background-color: #c00;
            color: white;
        }
        button.cancel {
            background-color: #666;
            color: white;
        }
    </style>
</head>
<body>
    <h2>Confirm Delete User</h2>
    <p>Are you sure you want to delete the user with ID: <strong><?= htmlspecialchars($id) ?></strong>?</p>

    <form method="POST">
        <button type="submit" name="confirm" value="yes" class="confirm">Yes, Delete</button>
        <button type="submit" name="confirm" value="no" class="cancel">Cancel</button>
    </form>
</body>
</html>
