<?php
require_once('config/db.php');
include('includes/header.php');

// Fetch latest books
$books_stmt = $conn->query("SELECT * FROM books ORDER BY created_at DESC LIMIT 6");

// Fetch 3 recent reviews + book & user info
$reviews_stmt = $conn->query("
    SELECT r.review_text, r.rating, r.created_at, u.name AS user_name, b.title AS book_title, b.id AS book_id
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN books b ON r.book_id = b.id
    ORDER BY r.created_at DESC
    LIMIT 3
");
?>

<!-- Hero Section -->
<div class="p-4 p-md-5 mb-4 bg-light rounded text-center shadow-sm">
    <h1 class="display-5 fw-bold">📚 Book Review System</h1>
    <p class="lead">Discover, review, and get recommendations for books you’ll love.</p>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="auth/register.php" class="btn btn-primary btn-lg">Join Now</a>
        <a href="auth/login.php" class="btn btn-outline-secondary btn-lg">Login</a>
    <?php else: ?>
        <a href="user/profile.php" class="btn btn-success btn-lg">Go to Profile</a>
    <?php endif; ?>
</div>

<!-- Latest Books -->
<h2 class="mb-4">📖 Latest Books</h2>
<div class="row row-cols-1 row-cols-md-3 g-4">
    <?php while ($book = $books_stmt->fetch_assoc()): ?>
        <div class="col">
            <div class="card h-100 shadow-sm">
                <img src="assets/images/<?php echo htmlspecialchars($book['cover_image']); ?>" class="card-img-top" alt="Book Cover">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                    <p class="card-text">By <?php echo htmlspecialchars($book['author']); ?></p>
                    <p class="card-text"><small class="text-muted"><?php echo htmlspecialchars($book['genre']); ?></small></p>
                    <a href="books/details.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<div class="text-center mt-4 mb-5">
    <a href="books/catalog.php" class="btn btn-outline-primary">Browse All Books</a>
</div>

<!-- Recent Reviews -->
<h2 class="mb-4">🌟 Featured Reviews</h2>
<div class="row row-cols-1 row-cols-md-3 g-4">
    <?php while ($review = $reviews_stmt->fetch_assoc()): ?>
        <div class="col">
            <div class="card h-100 border shadow-sm">
                <div class="card-body">
                    <h6 class="card-title"><?php echo htmlspecialchars($review['user_name']); ?> reviewed:</h6>
                    <p><strong><?php echo htmlspecialchars($review['book_title']); ?></strong></p>
                    <p class="text-warning mb-2">★ <?php echo $review['rating']; ?>/5</p>
                    <p class="card-text small"><?php echo nl2br(htmlspecialchars(substr($review['review_text'], 0, 100))); ?>...</p>
                </div>
                <div class="card-footer text-end">
                    <a href="books/details.php?id=<?php echo $review['book_id']; ?>" class="btn btn-sm btn-outline-secondary">Read More</a>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php include('includes/footer.php'); ?>
