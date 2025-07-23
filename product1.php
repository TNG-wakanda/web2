<?php
session_start();

// Database connection (only one now: orders)
$conn = new mysqli("localhost", "root", "", "orders");
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Step 1: Check if user is logged in
if (!isset($_SESSION['user'])) {
    die("⛔ You must be logged in to place an order.");
}

$user_id = $_SESSION['user']['id'];

// Step 2: Check if user exists in orders.users table
$stmt = $conn->prepare("SELECT id, fullnames, email FROM users WHERE id = ?");
if (!$stmt) {
    die("❌ Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Prompt user to enter details
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['fullnames']) && isset($_POST['email']) && !isset($_POST['product_id'])) {
        $fullnames = trim($_POST['fullnames']);
        $email = trim($_POST['email']);
        $password = password_hash("defaultpassword", PASSWORD_DEFAULT); // Default password
        $role = "user";

        $insert_user = $conn->prepare("INSERT INTO users (id, fullnames, email, password, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        if (!$insert_user) {
            die("❌ Insert prepare failed: " . $conn->error);
        }
        $insert_user->bind_param("issss", $user_id, $fullnames, $email, $password, $role);
        if ($insert_user->execute()) {
            echo "<p style='color:green;'>✅ User profile created. You can now place your order.</p>";
        } else {
            die("❌ Failed to save user: " . $insert_user->error);
        }
    } else {
        // Show form to enter fullnames and email
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Complete Your Profile</title>
            <style>
                body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px; }
                form { background: white; padding: 20px; border-radius: 10px; max-width: 400px; margin: auto; }
                input, button { width: 100%; padding: 10px; margin: 10px 0; font-size: 1em; }
                button { background: #007bff; color: white; border: none; cursor: pointer; }
                button:hover { background: #0056b3; }
            </style>
        </head>
        <body>
            <h2 style="text-align:center;">Complete Your Profile</h2>
            <form method="POST">
                <label>Full Names:</label>
                <input type="text" name="fullnames" required>
                <label>Email:</label>
                <input type="email" name="email" required>
                <button type="submit">Save and Continue</button>
            </form>
        </body>
        </html>
        <?php
        exit();
    }
}

// Products definition
$products = [
    1 => [
        "name" => "Fast Charging Power Bank",
        "price" => 15000,
        "photo" => "img/powerbank.jpg",
        "description" => "High capacity 20000mAh power bank with fast charging technology.",
    ],
    2 => [
        "name" => "Wireless Earbuds",
        "price" => 12000,
        "photo" => "img/earbuds.jpg",
        "description" => "Compact wireless earbuds with noise cancellation and 8-hour battery life.",
    ],
    3 => [
        "name" => "Smart Watch",
        "price" => 35000,
        "photo" => "img/watch.jpg",
        "description" => "Stylish smartwatch with heart rate monitor and multiple sports modes.",
    ],
    4 => [
        "name" => "Bluetooth Speaker",
        "price" => 25000,
        "photo" => "img/speaker.jpg",
        "description" => "portable Bluetooth Speaker with 10-hour battery life and deep bass.",
    ],
    5 => [
        "name" => "Smartphone",
        "price" => 120000,
        "photo" => "img/smartphone.jpg",
        "description" => "For calls, messaging, mobile apps, social media, and quick product photos.",
    ],
    6 => [
        "name" => "Laptop or Desktop Computer",
        "price" => 350000,
        "photo" => "img/laptop.jpg",
        "description" => "Used for inventory management, emails, order processing, and design work.",
    ],
    7 => [
        "name" => "POS (Point of Sale) System",
        "price" => 250000,
        "photo" => "img/pos.jpg",
        "description" => "For in-store sales, billing, and generating receipts efficiently.",
    ],
    8 => [
        "name" => "Barcode Scanner",
        "price" => 40000,
        "photo" => "img/barcode_scanner.jpg",
        "description" => "For fast and accurate product checkouts.",
    ],
    9 => [
        "name" => "Receipt Printer",
        "price" => 30000,
        "photo" => "img/receipt_printer.jpg",
        "description" => "Prints physical receipts for customer orders.",
    ],
    10 => [
        "name" => "Cash Drawer",
        "price" => 20000,
        "photo" => "img/cash_drawer.jpg",
        "description" => "Organizes and secures cash during transactions.",
    ],
    11 => [
        "name" => "Card Reader / Mobile Payment Device",
        "price" => 45000,
        "photo" => "img/card_reader.jpg",
        "description" => "Accept debit/credit card and mobile payments from customers.",
    ],
    12 => [
        "name" => "Label Printer",
        "price" => 60000,
        "photo" => "img/label_printer.jpg",
        "description" => "Prints shipping or product labels with ease.",
    ],
    13 => [
        "name" => "Wi-Fi Router",
        "price" => 25000,
        "photo" => "img/router.jpg",
        "description" => "Reliable internet connection is vital for online operations.",
    ],
    
    15 => [
        "name" => "Digital Camera",
        "price" => 80000,
        "photo" => "img/digital_camera.jpg",
        "description" => "For high-quality product photography if a smartphone isn't enough.",
    ],
    16 => [
        "name" => "Printer/Scanner",
        "price" => 40000,
        "photo" => "img/printer_scanner.jpg",
        "description" => "For printing invoices, contracts, or documentation needs.",
    ],
    17 => [
        "name" => "Tablet (Optional)",
        "price" => 100000,
        "photo" => "img/tablet.jpg",
        "description" => "Portable access to inventory, orders, or client presentations.",
    ],
    18 => [
        "name" => "Security Camera System",
        "price" => 150000,
        "photo" => "img/security_camera.jpg",
        "description" => "For shop or stockroom surveillance and protection.",
    ],
    19 => [
        "name" => "Flash Drive / External Hard Drive",
        "price" => 20000,
        "photo" => "img/flash_drive.jpg",
        "description" => "For backup and storage of important business data.",
    ],
];

// Step 3: Handle order submission (with product_id and quantity)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantity'], $_POST['product_id'])) {
    $quantity = intval($_POST['quantity']);
    $product_id = intval($_POST['product_id']);

    if ($quantity <= 0) {
        die("❗ Invalid quantity.");
    }
    if (!isset($products[$product_id])) {
        die("❗ Invalid product selected.");
    }

    // Insert order
    $status = 'pending';
    $order_stmt = $conn->prepare("INSERT INTO orders (user_id, order_date, status) VALUES (?, NOW(), ?)");
    if (!$order_stmt) {
        die("❌ Failed to prepare orders query: " . $conn->error);
    }
    $order_stmt->bind_param("is", $user_id, $status);

    if (!$order_stmt->execute()) {
        die("❌ Failed to save order: " . $order_stmt->error);
    }

    $order_id = $conn->insert_id;

    // Insert order item
    $product_name = $products[$product_id]['name'];
    $price = $products[$product_id]['price'];

    $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
    if (!$item_stmt) {
        die("❌ Failed to prepare order_items query: " . $conn->error);
    }
    $item_stmt->bind_param("isid", $order_id, $product_name, $quantity, $price);

    if ($item_stmt->execute()) {
        $success = "✅ Your order for <strong>{$product_name}</strong> has been placed!";
    } else {
        $error = "❌ Failed to save order item: " . $item_stmt->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Place Order - TNG</title>
    <style>
        /* Your existing header and nav styles */
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

        /* Products grid */
        .products-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin: 30px 20px;
        }

        .product-card {
            width: 220px;
            text-align: center;
            cursor: pointer;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 15px;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }
        .product-card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .product-card img {
            width: 100%;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        /* Product details & order form */
        #product-details {
            margin: 40px 20px;
            padding: 20px;
            border: 1px solid #aaa;
            border-radius: 10px;
            background: #fafafa;
            max-width: 700px;
            display: none;
        }
        #product-details img {
            width: 300px;
            border-radius: 15px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
        }

        #product-details p {
            max-width: 600px;
            font-size: 1.1em;
            line-height: 1.4em;
        }

        #product-details form {
            margin-top: 20px;
        }

        #product-details input[type="number"] {
            padding: 8px;
            width: 80px;
            font-size: 1em;
            margin-right: 10px;
        }

        #product-details button {
            padding: 8px 20px;
            font-size: 1em;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        #product-details button:hover {
            background: #0056b3;
        }

        /* Success and error messages */
        .message-success {
            color: green;
            font-weight: bold;
            margin: 20px;
        }

        .message-error {
            color: red;
            font-weight: bold;
            margin: 20px;
        }

        /* Mobile responsiveness */
        @media (max-width: 600px) {
            .products-container {
                flex-direction: column;
                margin: 10px;
            }

            .product-card {
                width: 90% !important;
                margin: 0 auto 20px auto;
            }

            #product-details {
                max-width: 95% !important;
                margin: 20px auto !important;
                padding: 15px !important;
            }

            #product-details img {
                width: 100% !important;
                height: auto !important;
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
    <h1><img src="img/logo2.jpg" alt="Logo">TNG</h1>
    <nav>
        <ul>
            <li><a href="webpage.php">Home</a></li>
            <li>
                <a href="#">Products</a>
                <div class="submenu">
                    <a href="product1.php">Product 
