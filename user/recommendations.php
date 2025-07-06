<?php
require_once('../config/db.php');
include('../includes/auth_session.php');
include('../includes/header.php');

$user_id = $_SESSION['user_id'];

// Get user's favorite genres from their reviews
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
    // Get recommended books in that genre, sorted by rating
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

<h2>Recommended Books</h2>
<?php if ($fav_genre): ?>
    <p>Based on your interest in <strong><?php echo htmlspecialchars($fav_genre); ?></strong> books.</p>

    <?php if ($books->num_rows > 0): ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php while ($book = $books->fetch_assoc()): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <img src="../assets/images/<?php echo htmlspecialchars($book['cover_image']); ?>" class="card-img-top" alt="Book Cover">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                            <p class="card-text">Author: <?php echo htmlspecialchars($book['author']); ?></p>
                            <p class="card-text"><small class="text-muted"><?php echo htmlspecialchars($book['genre']); ?></small></p>
                            <a href="../books/details.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No recommended books found.</div>
    <?php endif; ?>

<?php else: ?>
    <div class="alert alert-warning">You haven't reviewed any books yet. Review books to get recommendations!</div>
<?php endif; ?>

<?php include('../includes/footer.php'); ?>
