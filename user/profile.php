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

<div class="container py-5">
    <!-- Profile Header -->
    <h2 class="mb-4 fw-bold text-primary"><i class="fas fa-user-circle me-2"></i>Your Profile</h2>

    <!-- User Info -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="row g-0 align-items-center">
            <div class="col-md-3 text-center p-3">
                <?php if (!empty($user['profile_picture'])): ?>
                    <img src="/book-review-system/assets/images/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="img-fluid rounded-circle border" style="width: 120px; height: 120px; object-fit: cover;">
                <?php else: ?>
                    <i class="fas fa-user-circle fa-7x text-muted"></i>
                <?php endif; ?>
            </div>
            <div class="col-md-9">
                <div class="card-body">
                    <p class="mb-2"><strong><i class="fas fa-user me-2"></i>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                    <p class="mb-2"><strong><i class="fas fa-envelope me-2"></i>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p class="mb-2"><strong><i class="fas fa-phone me-2"></i>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                    <p class="mb-2"><strong><i class="fas fa-info-circle me-2"></i>Bio:</strong> <?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
                    <a href="edit_profile.php" class="btn btn-sm btn-outline-primary mt-3 me-2"> <i class="fas fa-edit me-1"></i>Edit Profile</a>
<a href="/book-review-system/auth/reset_password.php" class="btn btn-sm btn-outline-warning mt-3"><i class="fas fa-key me-1"></i>Change Password</a>

                </div>
            </div>
        </div>
    </div>

    <!-- User Reviews -->
    <h4 class="mb-3 text-secondary"><i class="fas fa-comment-dots me-2"></i>Your Reviews</h4>

    <?php if ($reviews->num_rows > 0): ?>
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <?php while ($review = $reviews->fetch_assoc()): ?>
                <div class="col">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <h6 class="card-title text-dark">
                                <i class="fas fa-book me-1"></i><?php echo htmlspecialchars($review['title']); ?>
                            </h6>
                            <span class="badge bg-warning text-dark mb-2">★ <?php echo $review['rating']; ?>/5</span>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                        </div>
                        <div class="card-footer bg-light text-end">
                            <small class="text-muted"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></small>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle me-1"></i> You haven't posted any reviews yet.
        </div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
