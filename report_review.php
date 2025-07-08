<?php
require_once('config/db.php');
include('includes/auth_session.php');
include('includes/header.php');

// Ensure a valid review ID is passed
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Invalid review ID.</div></div>";
    include('includes/footer.php');
    exit();
}

$review_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reason = trim($_POST['reason']);

    if (empty($reason)) {
        $message = "❌ Please enter a reason for reporting.";
    } else {
        $stmt = $conn->prepare("INSERT INTO reports (review_id, user_id, reason) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $review_id, $user_id, $reason);

        if ($stmt->execute()) {
            $message = "✅ Thank you! Your report has been submitted.";
            echo "<script>
                alert('Report submitted successfully.');
                window.location.href = 'books/details.php?id=" . $_GET['book_id'] . "';
            </script>";
            exit();
        } else {
            $message = "❌ Failed to submit report.";
        }
    }
}
?>

<div class="container py-5">
    <h3 class="mb-4 fw-bold text-danger"><i class="fas fa-flag me-2"></i>Report Review</h3>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-info"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Reason for Reporting</label>
                    <textarea name="reason" class="form-control" rows="5" placeholder="Explain why you're reporting this review..." required></textarea>
                </div>
                <button type="submit" class="btn btn-danger"><i class="fas fa-paper-plane me-1"></i>Submit Report</button>
                <a href="books/details.php?id=<?php echo isset($_GET['book_id']) ? intval($_GET['book_id']) : ''; ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
