<?php
// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// PHPMailer source files
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

require_once('config/db.php');
session_start();

// Allow only admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    die("❌ Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (!$user_id || !$subject || !$message) {
        die("❌ All fields are required.");
    }

    // Get recipient email
    $stmt = $conn->prepare("SELECT email, name FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        die("❌ User not found.");
    }

    $user = $res->fetch_assoc();
    $to_email = $user['email'];
    $to_name = $user['name'];

    // Send using PHPMailer
    $mail = new PHPMailer(true);

    try {
        // SMTP config
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'shahzaibkiran4@gmail.com'; // your Gmail
        $mail->Password   = 'yizordkcnrurrijv';         // app password
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        // Sender and recipient
        $mail->setFrom('shahzaibkiran4@gmail.com', 'BookReview Admin');
        $mail->addAddress($to_email, $to_name);
        $mail->addReplyTo('shahzaibkiran4@gmail.com', 'BookReview Admin');

        // Message
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = nl2br(htmlspecialchars($message));
        $mail->AltBody = $message;

        $mail->send();
        echo "<script>alert('✅ Email sent to {$to_email}'); window.location.href = 'admin/dashboard.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('❌ Mail error: {$mail->ErrorInfo}'); window.history.back();</script>";
    }
} else {
    echo "❌ Invalid request.";
}
?>
