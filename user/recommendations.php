<?php
require_once('../config/db.php');
include('../includes/auth_session.php');
include('../includes/header.php');

$user_id = $_SESSION['user_id'];

// Get favorite genre
$genre_stmt = $conn->prepare("
    SELECT b.genre, COUNT(*) as total
    FROM reviews r
    JOIN books b ON r.book_id = b.id
    WHERE r.user_id = ?
    GROUP BY b.genre
    ORDER BY total DESC
    LIMIT 1
");
$genre_stmt->bind_param("i", $user_id);
$genre_stmt->execute();
$genre_result = $genre_stmt->get_result();
$fav_genre = $genre_result->num_rows > 0 ? $genre_result->fetch_assoc()['genre'] : null;

$books = [];

if ($fav_genre) {
    $recommend_stmt = $conn->prepare("
        SELECT b.*, AVG(r.rating) as avg_rating
        FROM books b
        LEFT JOIN reviews r ON b.id = r.book_id
        WHERE b.genre = ?
        GROUP BY b.id
        ORDER BY avg_rating DESC
        LIMIT 6
    ");
    $recommend_stmt->bind_param("s", $fav_genre);
    $recommend_stmt->execute();
    $books = $recommend_stmt->get_result();
}
?>

<div class="container py-5">
    <h2 class="mb-3 fw-bold text-success"><i class="fas fa-star me-2"></i>Recommended Books</h2>

    <?php if ($fav_genre): ?>
        <p class="mb-4">Because you enjoy <span class="badge bg-info text-dark"><?php echo htmlspecialchars($fav_genre); ?></span> books, you might like these:</p>

        <?php if ($books->num_rows > 0): ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php while ($book = $books->fetch_assoc()): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0">
                            <img src="../assets/images/<?php echo htmlspecialchars($book['cover_image']); ?>" class="card-img-top" alt="Book Cover">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                                <p class="text-muted mb-1">By <?php echo htmlspecialchars($book['author']); ?></p>
                                <p class="mb-2"><small class="text-muted"><?php echo htmlspecialchars($book['genre']); ?></small></p>
                                <?php if ($book['avg_rating']): ?>
                                    <span class="badge bg-warning text-dark mb-2">★ <?php echo round($book['avg_rating'], 1); ?>/5</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary mb-2">No ratings yet</span>
                                <?php endif; ?>
                                <a href="../books/details.php?id=<?php echo $book['id']; ?>" class="btn btn-outline-primary btn-sm mt-2">
                                    <i class="fas fa-book-open me-1"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info mt-4"><i class="fas fa-info-circle me-2"></i>No recommended books found in that genre yet.</div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-warning mt-3">
            <i class="fas fa-exclamation-circle me-2"></i>You haven’t reviewed any books yet. Start reviewing to get personalized recommendations!
        </div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
