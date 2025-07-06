<?php
include('includes/header.php');

$message = "";
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $body = trim($_POST['message']);

    if ($name && $email && $subject && $body) {
        $to = "youremail@example.com"; // 🔁 Replace with your admin email
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        $full_message = "Name: $name\nEmail: $email\n\nMessage:\n$body";

        if (mail($to, $subject, $full_message, $headers)) {
            $success = true;
            $message = "✅ Your message has been sent successfully.";
        } else {
            $message = "❌ Failed to send message. Please try again later.";
        }
    } else {
        $message = "⚠️ Please fill in all fields.";
    }
}
?>

<div class="container">
    <h2>Contact Us</h2>

    <?php if ($message): ?>
        <div class="alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" required />
        </div>

        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required />
        </div>

        <div class="mb-3">
            <label class="form-label">Subject</label>
            <input type="text" name="subject" class="form-control" required />
        </div>

        <div class="mb-3">
            <label class="form-label">Message</label>
            <textarea name="message" class="form-control" rows="5" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
</div>

<?php include('includes/footer.php'); ?>
