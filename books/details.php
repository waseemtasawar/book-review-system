<?php
require_once('../config/db.php');
include('../includes/header.php');

if (!isset($_GET['id'])) {
    echo "<div class='container py-5 text-danger'><p>Invalid book ID.</p></div>";
    include('../includes/footer.php');
    exit();
}

$book_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

if (!$book) {
    echo "<div class='container py-5 text-danger'><p>Book not found.</p></div>";
    include('../includes/footer.php');
    exit();
}

// Check if book is in user's wishlist
$is_wishlisted = false;
if (isset($_SESSION['user_id'])) {
    $wishlist_check = $conn->prepare("SELECT * FROM wishlists WHERE user_id = ? AND book_id = ?");
    $wishlist_check->bind_param("ii", $_SESSION['user_id'], $book_id);
    $wishlist_check->execute();
    $wishlist_result = $wishlist_check->get_result();
    $is_wishlisted = $wishlist_result->num_rows > 0;
}

// Fetch reviews
$reviews_stmt = $conn->prepare("
    SELECT r.*, u.name, u.id AS user_id FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.book_id = ?
    ORDER BY r.created_at DESC
");
$reviews_stmt->bind_param("i", $book_id);
$reviews_stmt->execute();
$reviews = $reviews_stmt->get_result();
?>

<div class="container py-5">
    <!-- Book Info -->
    <div class="row mb-5">
        <div class="col-md-4">
            <img src="../assets/images/<?php echo htmlspecialchars($book['cover_image']); ?>" class="img-fluid rounded shadow-sm" alt="Book Cover">
        </div>
        <div class="col-md-8">
            <h2 class="fw-bold mb-3"><?php echo htmlspecialchars($book['title']); ?></h2>
            <p><strong><i class="fas fa-user me-1"></i>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
            <p><strong><i class="fas fa-tags me-1"></i>Genre:</strong> <span class="badge bg-secondary"><?php echo htmlspecialchars($book['genre']); ?></span></p>
            <p><strong><i class="fas fa-calendar-alt me-1"></i>Published:</strong> <?php echo htmlspecialchars($book['publication_year']); ?></p>
            <p class="mt-4"><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>

           <?php if (isset($_SESSION['user_id'])): ?>
    <a href="../reviews/add_review.php?book_id=<?php echo $book_id; ?>" class="btn btn-success mt-3 me-2">
        <i class="fas fa-pen me-1"></i> Add a Review
    </a>

    <form method="POST" action="/book-review-system/user/wishlist_toggle.php" class="d-inline">
        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
        <button type="submit" class="btn btn-outline-secondary mt-3">
            <i class="fas fa-heart me-1"></i> Add to Wishlist
        </button>
    </form>
<?php endif; ?>

        </div>
    </div>

    <!-- Reviews -->
    <h4 class="text-primary mb-4"><i class="fas fa-comments me-2"></i>Reader Reviews</h4>

    <?php if ($reviews->num_rows > 0): ?>
        <?php while($review = $reviews->fetch_assoc()): ?>
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong><i class="fas fa-user-circle me-1"></i><?php echo htmlspecialchars($review['name']); ?></strong>
                            <span class="badge bg-warning text-dark ms-2">★ <?php echo $review['rating']; ?>/5</span>
                        </div>
                        <small class="text-muted"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></small>
                    </div>
                    <p class="mb-2"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>

                    <div>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $review['user_id']): ?>
                            <a href="../reviews/edit_review.php?id=<?php echo $review['id']; ?>" class="btn btn-sm btn-outline-primary me-2">Edit</a>
                            <a href="../reviews/delete_review.php?id=<?php echo $review['id']; ?>" class="btn btn-sm btn-outline-danger me-2" onclick="return confirm('Are you sure you want to delete this review?')">Delete</a>
                        <?php elseif (isset($_SESSION['user_id'])): ?>
                            <a href="../report_review.php?id=<?php echo $review['id']; ?>&book_id=<?php echo $book_id; ?>" class="btn btn-sm btn-outline-warning">Report</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-1"></i> No reviews yet. Be the first to share your thoughts!
        </div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
