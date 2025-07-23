<?php
session_start();
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = (int)$_SESSION['user']['id'];
// Adjust to match your session key case
$user_email = $_SESSION['user']['Email'] ?? '';

$conn = new mysqli("localhost", "root", "", "Register");
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['message']) || trim($data['message']) === '') {
    echo json_encode(['error' => 'Message is empty']);
    exit();
}

$message = trim($data['message']);

// Store user message
$stmt = $conn->prepare("INSERT INTO messages (user_id, message, created_at) VALUES (?, ?, NOW())");
if (!$stmt) {
    echo json_encode(['error' => 'Failed to prepare statement']);
    exit();
}
$stmt->bind_param("is", $user_id, $message);
$stmt->execute();
$last_id = $stmt->insert_id;
$stmt->close();

// PHPMailer part
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'nshimiyimanagad24@gmail.com';
    $mail->Password   = 'TNG@37717';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('no-reply@tng.com', 'TNG Chat System');
    $mail->addAddress('nshimiyimanagad24@gmail.com', 'TNG Admin');
    if (!empty($user_email)) {
        $mail->addReplyTo($user_email);
    }

    $mail->isHTML(false);
    $mail->Subject = "New message from TNG user (ID: $user_id)";
    $mail->Body    = "You received a new message:\n\nFrom: $user_email (User ID: $user_id)\n\nMessage:\n$message";

    $mail->send();
} catch (Exception $e) {
    error_log("Mailer Error: " . $mail->ErrorInfo);
}

// Auto-reply from admin (bot)
$auto_reply = "Thank you for your message. We will reply shortly.";
$stmt = $conn->prepare("UPDATE messages SET reply = ?, replied_at = NOW() WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("si", $auto_reply, $last_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

echo json_encode(['reply' => $auto_reply]);
