<?php
require_once('../config/db.php');
include('../includes/auth_session.php');
include('../includes/header.php');

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT b.* FROM wishlists w
    JOIN books b ON w.book_id = b.id
    WHERE w.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$books = $stmt->get_result();
?>

<h2>Your Wishlist</h2>

<?php if ($books->num_rows > 0): ?>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php while ($book = $books->fetch_assoc()): ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <img src="../assets/images/<?php echo htmlspecialchars($book['cover_image']); ?>" class="card-img-top" alt="Book Cover">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                        <p class="card-text">Author: <?php echo htmlspecialchars($book['author']); ?></p>
                        <a href="../books/details.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <p>You haven't added any books to your wishlist yet.</p>
<?php endif; ?>

<?php include('../includes/footer.php'); ?>
