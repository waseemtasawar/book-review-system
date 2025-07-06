<?php
require_once('config/db.php');

// Optional: Only allow admin or server processes
session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    die("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (!$user_id || !$subject || !$message) {
        die("Missing required fields.");
    }

    // Get user email
    $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        die("User not found.");
    }

    $user = $res->fetch_assoc();
    $to = $user['email'];

    $headers = "From: noreply@bookreview.local\r\n";
    $headers .= "Reply-To: noreply@bookreview.local\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if (mail($to, $subject, $message, $headers)) {
        echo "✅ Notification sent to {$to}";
    } else {
        echo "❌ Failed to send email.";
    }
} else {
    echo "No POST data received.";
}
?>
