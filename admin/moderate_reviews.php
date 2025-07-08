<?php
require_once('../config/db.php');
include('../includes/auth_session.php');
include('../includes/header.php');

if (!$_SESSION['is_admin']) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Access denied. Admins only.</div></div>";
    include('../includes/footer.php');
    exit();
}

// Handle deletion
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $conn->query("DELETE FROM reviews WHERE id = $delete_id");
    echo "<div class='container py-3'><div class='alert alert-success'>Review deleted successfully.</div></div>";
}

// Fetch all reviews
$reviews = $conn->query("
    SELECT r.*, u.name AS user_name, b.title AS book_title
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN books b ON r.book_id = b.id
    ORDER BY r.created_at DESC
");
?>

<div class="container py-5">
    <h2 class="mb-4 fw-bold text-primary"><i class="fas fa-comments me-2"></i>Moderate Reviews</h2>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Book</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($review = $reviews->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($review['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($review['book_title']); ?></td>
                                <td>
                                    <span class="badge bg-warning text-dark">★ <?php echo $review['rating']; ?>/5</span>
                                </td>
                                <td><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></td>
                                <td><?php echo date("M d, Y", strtotime($review['created_at'])); ?></td>
                                <td>
                                    <a href="?delete=<?php echo $review['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Are you sure you want to delete this review?');">
                                       <i class="fas fa-trash-alt me-1"></i>Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
