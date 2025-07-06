<?php
require_once('../config/db.php');
include('../includes/auth_session.php');
include('../includes/header.php');

// Only admins allowed
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo "<div class='alert alert-danger'>Access denied.</div>";
    include('../includes/footer.php');
    exit();
}
?>

<div class="container">
    <h2>Send Email Notification</h2>
    <form method="POST" action="../send_notification.php" class="mt-4">
        <div class="mb-3">
            <label class="form-label">User ID</label>
            <input type="number" name="user_id" class="form-control" placeholder="Enter User ID" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Subject</label>
            <input type="text" name="subject" class="form-control" placeholder="Email subject" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Message</label>
            <textarea name="message" class="form-control" rows="5" placeholder="Type the message here" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Email</button>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
