<?php
require_once('config/db.php');
include('includes/auth_session.php');
include('includes/header.php');

$review_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];
$message = "";

// Get review details
$stmt = $conn->prepare("
    SELECT r.review_text, u.name, b.title
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN books b ON r.book_id = b.id
    WHERE r.id = ?
");
$stmt->bind_param("i", $review_id);
$stmt->execute();
$review = $stmt->get_result()->fetch_assoc();

if (!$review) {
    echo "<div class='alert alert-danger'>Review not found.</div>";
    include('includes/footer.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reason = trim($_POST['reason']);

    if ($reason) {
        $report_stmt = $conn->prepare("INSERT INTO reports (review_id, user_id, reason) VALUES (?, ?, ?)");
        $report_stmt->bind_param("iis", $review_id, $user_id, $reason);
        if ($report_stmt->execute()) {
            $message = "✅ Your report has been submitted for moderation.";
        } else {
            $message = "❌ Failed to submit report.";
        }
    } else {
        $message = "⚠️ Please provide a reason.";
    }
}
?>

<div class="container">
    <h2>Report Review</h2>

    <?php if ($message): ?>
        <div class="alert <?php echo str_starts_with($message, '✅') ? 'alert-success' : 'alert-danger'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="card mb-3">
        <div class="card-header">
            Review from <?php echo htmlspecialchars($review['name']); ?> on "<?php echo htmlspecialchars($review['title']); ?>"
        </div>
        <div class="card-body">
            <p><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
        </div>
    </div>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Why are you reporting this review?</label>
            <textarea name="reason" class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-danger">Submit Report</button>
        <a href="books/details.php?id=<?php echo $review_id; ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('includes/footer.php'); ?>
