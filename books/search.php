<?php
require_once('../config/db.php');
include('../includes/header.php');

$search = isset($_GET['query']) ? trim($_GET['query']) : '';

if (empty($search)) {
    echo "<div class='container py-5 text-danger'><p>No search query provided.</p></div>";
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

<div class="container py-5">
    <!-- Search Heading -->
    <h2 class="mb-4 text-primary fw-bold">
        <i class="fas fa-search me-2"></i>Results for: <span class="text-dark">"<?php echo htmlspecialchars($search); ?>"</span>
    </h2>

    <?php if ($result->num_rows > 0): ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php while ($book = $result->fetch_assoc()): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="../assets/images/<?php echo htmlspecialchars($book['cover_image']); ?>" class="card-img-top" alt="Book Cover">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                            <p class="text-muted mb-1"><i class="fas fa-user me-1"></i><?php echo htmlspecialchars($book['author']); ?></p>
                            <p><span class="badge bg-secondary"><?php echo htmlspecialchars($book['genre']); ?></span></p>
                            <a href="details.php?id=<?php echo $book['id']; ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-book-open me-1"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning mt-4">
            <i class="fas fa-exclamation-circle me-2"></i>No books found matching your search.
        </div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
