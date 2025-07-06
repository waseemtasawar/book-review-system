<?php
require_once('../config/db.php');
include('../includes/header.php');

$search = isset($_GET['query']) ? trim($_GET['query']) : '';

if (empty($search)) {
    echo "<p>No search query provided.</p>";
    include('../includes/footer.php');
    exit();
}

$query = "SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR genre LIKE ?";
$stmt = $conn->prepare($query);
$like = "%$search%";
$stmt->bind_param("sss", $like, $like, $like);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2 class="mb-4">Search Results for "<?php echo htmlspecialchars($search); ?>"</h2>

<?php if ($result->num_rows > 0): ?>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php while ($book = $result->fetch_assoc()): ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <img src="../assets/images/<?php echo htmlspecialchars($book['cover_image']); ?>" class="card-img-top" alt="Book Cover">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                        <p class="card-text">Author: <?php echo htmlspecialchars($book['author']); ?></p>
                        <p class="card-text"><small class="text-muted"><?php echo htmlspecialchars($book['genre']); ?></small></p>
                        <a href="details.php?id=<?php echo $book['id']; ?>" class="btn btn-primary btn-sm">View Details</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="alert alert-warning">No books found matching your search.</div>
<?php endif; ?>

<?php include('../includes/footer.php'); ?>
