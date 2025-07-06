<?php
require_once('../config/db.php');
include('../includes/auth_session.php');
include('../includes/header.php');

$review_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];
$message = "";

// Fetch existing review
$stmt = $conn->prepare("SELECT * FROM reviews WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $review_id, $user_id);
$stmt->execute();
$review = $stmt->get_result()->fetch_assoc();

if (!$review) {
    echo "<p>Review not found or access denied.</p>";
    include('../includes/footer.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_rating = intval($_POST['rating']);
    $new_text = trim($_POST['review_text']);

    $update = $conn->prepare("UPDATE reviews SET rating = ?, review_text = ? WHERE id = ?");
    $update->bind_param("isi", $new_rating, $new_text, $review_id);
    if ($update->execute()) {
        header("Location: ../books/details.php?id=" . $review['book_id']);
        exit();
    } else {
        $message = "❌ Failed to update review.";
    }
}
?>

<div class="container">
    <h3>Edit Your Review</h3>
    <?php if ($message): ?>
        <div class="alert alert-danger"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Rating (1-5)</label>
            <input type="number" name="rating" class="form-control" min="1" max="5" value="<?php echo $review['rating']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Review</label>
            <textarea name="review_text" class="form-control" rows="5" required><?php echo htmlspecialchars($review['review_text']); ?></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Update Review</button>
        <a href="../books/details.php?id=<?php echo $review['book_id']; ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
