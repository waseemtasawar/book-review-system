<?php
require_once('../config/db.php');
include('../includes/header.php');

if (!isset($_GET['id'])) {
    echo "<p>Invalid book ID.</p>";
    include('../includes/footer.php');
    exit();
}

$book_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

if (!$book) {
    echo "<p>Book not found.</p>";
    include('../includes/footer.php');
    exit();
}

// Get reviews
$reviews_stmt = $conn->prepare("
    SELECT r.*, u.name FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.book_id = ?
    ORDER BY r.created_at DESC
");
$reviews_stmt->bind_param("i", $book_id);
$reviews_stmt->execute();
$reviews = $reviews_stmt->get_result();
?>

<h2><?php echo htmlspecialchars($book['title']); ?></h2>
<p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
<p><strong>Genre:</strong> <?php echo htmlspecialchars($book['genre']); ?></p>
<p><strong>Published:</strong> <?php echo htmlspecialchars($book['publication_year']); ?></p>
<p><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>

<?php if (isset($_SESSION['user_id'])): ?>
    <a href="../reviews/add_review.php?book_id=<?php echo $book_id; ?>" class="btn btn-success mb-3">Add a Review</a>
<?php endif; ?>

<hr>
<h4>Reviews</h4>
<?php if ($reviews->num_rows > 0): ?>
    <?php while($review = $reviews->fetch_assoc()): ?>
        <div class="border rounded p-3 mb-3">
            <strong><?php echo htmlspecialchars($review['name']); ?></strong>
            <span class="text-warning">★ <?php echo $review['rating']; ?>/5</span>
            <p><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
            <small class="text-muted"><?php echo $review['created_at']; ?></small>

            <div class="mt-2">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $review['user_id']): ?>
                    <a href="../reviews/edit_review.php?id=<?php echo $review['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                    <a href="../reviews/delete_review.php?id=<?php echo $review['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this review?')">Delete</a>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $review['user_id']): ?>
                    <a href="../report_review.php?id=<?php echo $review['id']; ?>" class="btn btn-sm btn-outline-warning">Report</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No reviews yet.</p>
<?php endif; ?>

<?php include('../includes/footer.php'); ?>
