<?php
session_start();

// Show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    die("⛔ You must be logged in.");
}

$user_id = $_SESSION['user']['id'];
$default_email = $_SESSION['user']['email'] ?? '';

$success = "";
$error = "";

// ✅ Connect to REGISTER database first (to verify user)
$connRegister = new mysqli("localhost", "root", "", "register");
if ($connRegister->connect_error) {
    die("❌ Could not connect to register DB: " . $connRegister->connect_error);
}

// Confirm user exists
$stmtUser = $connRegister->prepare("SELECT id FROM register WHERE id = ?");
if (!$stmtUser) {
    die("Prepare failed in register DB: " . $connRegister->error);
}
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
if ($resultUser->num_rows === 0) {
    die("❌ User not found in register DB.");
}

// ✅ Now connect to ORDERS DB
$connOrders = new mysqli("localhost", "root", "", "orders");
if ($connOrders->connect_error) {
    die("❌ Could not connect to orders DB: " . $connOrders->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = substr(trim($_POST['product_name'] ?? ''), 0, 100);
    $price = floatval($_POST['price'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);
    $email = trim($_POST['email'] ?? '');

    if ($product_name === '') {
        $error = "❗ Product name is required.";
    } elseif ($price <= 0) {
        $error = "❗ Price must be greater than zero.";
    } elseif ($quantity <= 0) {
        $error = "❗ Quantity must be at least 1.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "❗ Please enter a valid email address.";
    }

    if (!$error) {
        $status = "pending";

        $stmtOrder = $connOrders->prepare("INSERT INTO orders (user_id, order_date, status) VALUES (?, NOW(), ?)");
        if (!$stmtOrder) {
            die("Prepare failed for orders: " . $connOrders->error);
        }

        $stmtOrder->bind_param("is", $user_id, $status);
        if (!$stmtOrder->execute()) {
            $error = "❌ Could not place order: " . $stmtOrder->error;
        } else {
            $order_id = $stmtOrder->insert_id;

            $stmtItem = $connOrders->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
            if (!$stmtItem) {
                die("Prepare failed for order_items: " . $connOrders->error);
            }

            $stmtItem->bind_param("isid", $order_id, $product_name, $quantity, $price);
            if (!$stmtItem->execute()) {
                $error = "❌ Could not add order item: " . $stmtItem->error;
            } else {
                $success = "✅ Order placed successfully for <strong>" . htmlspecialchars($product_name) . "</strong>!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Place an Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 30px;
        }

        form {
            background: white;
            padding: 20px;
            border-radius: 6px;
            max-width: 450px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }

        input, button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            box-sizing: border-box;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            font-weight: bold;
            font-size: 1.1rem;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .success {
            color: green;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .error {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        h2 {
            text-align: center;
        }
    </style>
</head>
<body>

<?php if ($success): ?>
    <p class="success"><?= $success ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p class="error"><?= $error ?></p>
<?php endif; ?>

<form method="POST" autocomplete="off">
    <h2>Order a Product</h2>

    <label for="product_name">Product Name:</label>
    <input
        type="text"
        id="product_name"
        name="product_name"
        placeholder="Enter product name..."
        maxlength="100"
        required
        value="<?= htmlspecialchars($_POST['product_name'] ?? '') ?>"
    />

    <label for="price">Price (RWF):</label>
    <input
        type="number"
        id="price"
        name="price"
        placeholder="Enter price"
        min="0.01"
        step="0.01"
        required
        value="<?= htmlspecialchars($_POST['price'] ?? '') ?>"
    />

    <label for="quantity">Quantity:</label>
    <input
        type="number"
        id="quantity"
        name="quantity"
        min="1"
        required
        value="<?= intval($_POST['quantity'] ?? 1) ?>"
    />

    <label for="email">Email:</label>
    <input
        type="email"
        id="email"
        name="email"
        placeholder="Enter your email"
        required
        value="<?= htmlspecialchars($_POST['email'] ?? $default_email) ?>"
    />

    <button type="submit">Place Order</button>
</form>

</body>
</html>
