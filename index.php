<?php
require_once('config/db.php');
include('includes/header.php');

// Fetch latest books
$books_stmt = $conn->query("SELECT * FROM books ORDER BY created_at DESC LIMIT 6");

// Fetch recent reviews
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
<section class="py-5 text-center text-white" style="background: linear-gradient(90deg, #2c3e50, #4b6cb7);">
    <div class="container">
        <h1 class="display-4 fw-bold">📚 Welcome to Book Review System</h1>
        <p class="lead">Discover your next favorite read. Review books. Get smart recommendations. Connect with fellow readers.</p>
        <div class="mt-4">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="auth/register.php" class="btn btn-warning btn-lg me-2"><i class="fas fa-user-plus me-1"></i>Join Now</a>
                <a href="auth/login.php" class="btn btn-outline-light btn-lg"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
            <?php else: ?>
                <a href="user/profile.php" class="btn btn-success btn-lg"><i class="fas fa-user me-1"></i>Go to Profile</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Latest Books -->
<section class="container py-5">
    <h2 class="mb-4 text-primary fw-bold"><i class="fas fa-book-open me-2"></i>Latest Books</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php while ($book = $books_stmt->fetch_assoc()): ?>
            <div class="col">
                <div class="card h-100 shadow-sm border-0">
                    <img src="assets/images/<?php echo htmlspecialchars($book['cover_image']); ?>" class="card-img-top" alt="Book Cover">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                        <p class="text-muted mb-1">By <?php echo htmlspecialchars($book['author']); ?></p>
                        <p><span class="badge bg-secondary"><?php echo htmlspecialchars($book['genre']); ?></span></p>
                        <a href="books/details.php?id=<?php echo $book['id']; ?>" class="btn btn-outline-primary btn-sm">View Details</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <div class="text-center mt-5">
        <a href="books/catalog.php" class="btn btn-outline-dark px-4"><i class="fas fa-layer-group me-1"></i>Browse All Books</a>
    </div>
</section>

<!-- Featured Reviews -->
<section class="container pb-5">
    <h2 class="mb-4 text-primary fw-bold"><i class="fas fa-star me-2"></i>Featured Reviews</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php while ($review = $reviews_stmt->fetch_assoc()): ?>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title text-secondary"><i class="fas fa-user me-1"></i><?php echo htmlspecialchars($review['user_name']); ?> reviewed:</h6>
                        <p><strong><?php echo htmlspecialchars($review['book_title']); ?></strong></p>
                        <p class="text-warning mb-2">★ <?php echo $review['rating']; ?>/5</p>
                        <p class="card-text small">
                            <?php echo nl2br(htmlspecialchars(substr($review['review_text'], 0, 100))); ?>...
                        </p>
                    </div>
                    <div class="card-footer text-end bg-light">
                        <a href="books/details.php?id=<?php echo $review['book_id']; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-book-reader me-1"></i>Read More</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php include('includes/footer.php'); ?>
