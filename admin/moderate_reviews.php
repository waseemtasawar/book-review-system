<?php
require_once('../config/db.php');
include('../includes/auth_session.php');
include('../includes/header.php');

if (!$_SESSION['is_admin']) {
    echo "<div class='alert alert-danger'>Access denied. Admins only.</div>";
    include('../includes/footer.php');
    exit();
}

// Delete review if requested
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $conn->query("DELETE FROM reviews WHERE id = $delete_id");
    echo "<div class='alert alert-success'>Review deleted.</div>";
}

$reviews = $conn->query("
    SELECT r.*, u.name AS user_name, b.title AS book_title
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN books b ON r.book_id = b.id
    ORDER BY r.created_at DESC
");
?>

<h2>Moderate Reviews</h2>

<table class="table table-bordered table-hover">
    <thead>
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
                <td><?php echo $review['rating']; ?></td>
                <td><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></td>
                <td><?php echo $review['created_at']; ?></td>
                <td>
                    <a href="?delete=<?php echo $review['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this review?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include('../includes/footer.php'); ?>
