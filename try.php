<?php
// Connect to the first database (e.g., register)
$conn1 = new mysqli("localhost", "root", "", "register");
if ($conn1->connect_error) {
    die("❌ Connection to 'register' failed: " . $conn1->connect_error);
}

// Connect to the second database (e.g., orders)
$conn2 = new mysqli("localhost", "root", "", "orders");
if ($conn2->connect_error) {
    die("❌ Connection to 'orders' failed: " . $conn2->connect_error);
}

// Example: Fetch from register D
$result1 = $conn2->query("SELECT * FROM users");

?>
