<?php
require_once('../config/db.php');
include('../includes/header.php');

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$query = "SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR genre LIKE ?";
$stmt = $conn->prepare($query);
$search_param = "%$search%";
$stmt->bind_param("sss", $search_param, $search_param, $search_param);
$stmt->execute();
$books = $stmt->get_result();
?>

<div class="container py-5">
    <h2 class="mb-4 text-primary fw-bold"><i class="fas fa-book me-2"></i>Book Catalog</h2>

    <!-- Search Bar -->
    <form method="GET" action="catalog.php" class="mb-5">
        <div class="input-group shadow-sm">
            <input type="text" name="search" class="form-control" placeholder="Search by title, author, or genre..." value="<?php echo htmlspecialchars($search); ?>" />
            <button class="btn btn-warning" type="submit"><i class="fas fa-search me-1"></i> Search</button>
        </div>
    </form>

    <!-- Book Results -->
    <?php if ($books->num_rows > 0): ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php while ($book = $books->fetch_assoc()): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="../assets/images/<?php echo htmlspecialchars($book['cover_image']); ?>" class="card-img-top" alt="Book Cover">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                            <p class="text-muted mb-1">By <?php echo htmlspecialchars($book['author']); ?></p>
                            <p><span class="badge bg-secondary"><?php echo htmlspecialchars($book['genre']); ?></span></p>
                            <a href="details.php?id=<?php echo $book['id']; ?>" class="btn btn-outline-primary btn-sm"><i class="fas fa-book-open me-1"></i> View Details</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info mt-4">
            <i class="fas fa-info-circle me-1"></i> No books found matching your search.
        </div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
