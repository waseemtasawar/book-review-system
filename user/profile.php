<?php
require_once('../config/db.php');
include('../includes/auth_session.php');
include('../includes/header.php');

$user_id = $_SESSION['user_id'];

// Get user info
$user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

// Get user's reviews
$review_stmt = $conn->prepare("
    SELECT r.*, b.title FROM reviews r
    JOIN books b ON r.book_id = b.id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
");
$review_stmt->bind_param("i", $user_id);
$review_stmt->execute();
$reviews = $review_stmt->get_result();
?>

<h2>Your Profile</h2>
<p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
<p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
<p><strong>Bio:</strong> <?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>

<a href="edit_profile.php" class="btn btn-primary btn-sm">Edit Profile</a>

<hr>
<h4>Your Reviews</h4>
<?php if ($reviews->num_rows > 0): ?>
    <?php while ($review = $reviews->fetch_assoc()): ?>
        <div class="border rounded p-3 mb-3">
            <strong>Book:</strong> <?php echo htmlspecialchars($review['title']); ?><br>
            <span class="text-warning">★ <?php echo $review['rating']; ?>/5</span>
            <p><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
            <small class="text-muted"><?php echo $review['created_at']; ?></small>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>You haven't posted any reviews yet.</p>
<?php endif; ?>

<?php include('../includes/footer.php'); ?>
