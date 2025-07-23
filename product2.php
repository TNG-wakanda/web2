<?php
session_start();

// Check if user is logged in (session exists)
if (!isset($_SESSION['user'])) {
    die("⛔ Please login first.");
}
$user_id = $_SESSION['user']['id'];

// Connect to Register DB to verify user exists
$connRegister = new mysqli("localhost", "root", "", "register");
if ($connRegister->connect_error) {
    die("❌ Register DB connection error: " . $connRegister->connect_error);
}

// Check if user exists in register table
$stmtUser = $connRegister->prepare("SELECT id FROM register WHERE id = ?");
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();

if ($resultUser->num_rows === 0) {
    die("⛔ User not found in register database. Please login again.");
}
$stmtUser->close();
$connRegister->close();

// Products list
$products = [
    1 => [
        "name" => "Smartphone",
        "price" => 120000,
        "photo" => "img/smartphone.jpg",
    ],
    2 => [
        "name" => "Laptop",
        "price" => 350000,
        "photo" => "img/laptop.jpg",
    ],
    3 => [
        "name" => "Tablet",
        "price" => 200000,
        "photo" => "img/tablet.jpg",
    ],
    4 => [
        "name" => "Fast Charging Power Bank",
        "price" => 50000,
        "photo" => "img/powerbank.jpg",
    ],
];

// Connect to orders DB for placing orders
$connOrders = new mysqli("localhost", "root", "", "orders");
if ($connOrders->connect_error) {
    die("❌ Orders DB connection error: " . $connOrders->connect_error);
}

// Handle form submission
$success = $error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? '';
    $quantity = intval($_POST['quantity'] ?? 1);

    if (!isset($products[$product_id])) {
        $error = "❌ Invalid product selected.";
    } elseif ($quantity < 1) {
        $error = "❌ Quantity must be at least 1.";
    } else {
        $product = $products[$product_id];
        $status = "pending";

        // Insert into orders table
        $stmtOrder = $connOrders->prepare("INSERT INTO orders (user_id, order_date, status) VALUES (?, NOW(), ?)");
        $stmtOrder->bind_param("is", $user_id, $status);

        if ($stmtOrder->execute()) {
            $order_id = $stmtOrder->insert_id;

            // Insert into order_items table
            $stmtItem = $connOrders->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
            $stmtItem->bind_param("isid", $order_id, $product['name'], $quantity, $product['price']);

            if ($stmtItem->execute()) {
                $success = "✅ Order placed successfully for <strong>" . htmlspecialchars($product['name']) . "</strong>!";
            } else {
                $error = "❌ Could not save order item: " . $stmtItem->error;
            }

            $stmtItem->close();
        } else {
            $error = "❌ Could not place order: " . $stmtOrder->error;
        }

        $stmtOrder->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Order</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        /* Reset and base */
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            margin: 0;
            background: #fafafa;
        }
        h2 {
            margin-bottom: 15px;
            text-align: center;
        }
        .products-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .product-card {
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 15px;
            width: 200px;
            text-align: center;
            background: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: transform 0.15s ease-in-out;
        }
        .product-card:hover {
            transform: scale(1.03);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .product-card img {
            width: 100%;
            max-width: 150px;
            height: auto;
            object-fit: contain;
            border-radius: 10px;
            margin-bottom: 10px;
        }
        input[type="radio"] {
            margin-right: 6px;
            transform: scale(1.3);
            vertical-align: middle;
        }
        label {
            cursor: pointer;
            font-weight: bold;
            font-size: 1rem;
        }
        label[for^="product"] {
            display: inline-block;
            margin-top: 10px;
            font-weight: normal;
        }

        /* Quantity input */
        label[for="quantity"] {
            display: block;
            margin-top: 30px;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 1rem;
            text-align: center;
        }
        input[type="number"] {
            display: block;
            margin: 0 auto 20px auto;
            padding: 8px 10px;
            font-size: 1rem;
            width: 80px;
            border: 1px solid #ccc;
            border-radius: 6px;
            text-align: center;
        }

        /* Submit button */
        button {
            display: block;
            margin: 0 auto;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }

        /* Success and error messages */
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

        /* Responsive for phones */
        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            .products-row {
                gap: 15px;
            }
            .product-card {
                width: 100%;
                max-width: 300px;
                padding: 10px;
            }
            .product-card img {
                max-width: 100%;
                height: auto;
            }
            input[type="number"] {
                width: 60px;
            }
            button {
                width: 100%;
                font-size: 1rem;
                padding: 12px;
            }
        }
    </style>
</head>
<body>

<h2>🛍️ Choose a Product to Order</h2>

<?php if ($success): ?>
    <p class="success"><?= $success ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p class="error"><?= $error ?></p>
<?php endif; ?>

<form method="POST">
    <div class="products-row">
        <?php foreach ($products as $id => $p): ?>
            <div class="product-card">
                <img src="<?= htmlspecialchars($p['photo']) ?>" alt="<?= htmlspecialchars($p['name']) ?>"><br>
                <strong><?= htmlspecialchars($p['name']) ?></strong><br>
                <?= number_format($p['price']) ?> RWF<br>
                <input type="radio" name="product_id" value="<?= $id ?>" id="product<?= $id ?>" required>
                <label for="product<?= $id ?>">Select</label>
            </div>
        <?php endforeach; ?>
    </div>

    <label for="quantity">Quantity:</label>
    <input type="number" name="quantity" id="quantity" value="1" min="1" required>

    <button type="submit">✅ Place Order</button>
</form>

</body>
</html>
