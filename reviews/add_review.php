<?php
require_once('../config/db.php');
include('../includes/auth_session.php');
include('../includes/header.php');

$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating']);
    $review_text = trim($_POST['review_text']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO reviews (user_id, book_id, rating, review_text) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $user_id, $book_id, $rating, $review_text);

    if ($stmt->execute()) {
        header("Location: ../books/details.php?id=" . $book_id);
        exit();
    } else {
        $message = "❌ Failed to add review.";
    }
}
?>

<div class="container">
    <h3>Add Your Review</h3>
    <?php if ($message): ?>
        <div class="alert alert-danger"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Rating (1-5)</label>
            <input type="number" name="rating" class="form-control" min="1" max="5" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Review</label>
            <textarea name="review_text" class="form-control" rows="5" required></textarea>
        </div>
        <button class="btn btn-success" type="submit">Submit Review</button>
        <a href="../books/details.php?id=<?php echo $book_id; ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
