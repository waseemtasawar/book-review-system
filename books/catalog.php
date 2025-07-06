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

<h2 class="mb-4">Book Catalog</h2>

<form method="GET" action="search.php" class="mb-4">
    <div class="input-group">
        <input type="text" name="query" class="form-control" placeholder="Search by title, author, or genre" value="<?php echo htmlspecialchars($search); ?>">
        <button class="btn btn-outline-secondary" type="submit">Search</button>
    </div>
</form>

<div class="row row-cols-1 row-cols-md-3 g-4">
    <?php while($book = $books->fetch_assoc()): ?>
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

<?php include('../includes/footer.php'); ?>
