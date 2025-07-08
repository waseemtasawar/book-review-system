<?php
require_once('../config/db.php');
include('../includes/auth_session.php');

$review_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];
$message = "";

// Fetch existing review
$stmt = $conn->prepare("SELECT * FROM reviews WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $review_id, $user_id);
$stmt->execute();
$review = $stmt->get_result()->fetch_assoc();

if (!$review) {
    include('../includes/header.php');
    echo "<div class='container py-5'><div class='alert alert-danger'>Review not found or access denied.</div></div>";
    include('../includes/footer.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_rating = intval($_POST['rating']);
    $new_text = trim($_POST['review_text']);

    $update = $conn->prepare("UPDATE reviews SET rating = ?, review_text = ? WHERE id = ?");
    $update->bind_param("isi", $new_rating, $new_text, $review_id);

    if ($update->execute()) {
        header("Location: ../books/details.php?id=" . $review['book_id']);
        exit();
    } else {
        $message = "❌ Failed to update review.";
    }
}

include('../includes/header.php');
?>

<div class="container py-5">
    <h3 class="mb-4 fw-bold text-primary"><i class="fas fa-edit me-2"></i>Edit Your Review</h3>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <?php if ($message): ?>
                <div class="alert alert-danger d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="row g-3">
                <div class="col-12">
                    <label class="form-label">Rating</label>
                    <select name="rating" class="form-select" required>
                        <option value="">Select rating</option>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?php echo $i; ?>" <?php echo ($review['rating'] == $i) ? 'selected' : ''; ?>>
                                <?php echo $i; ?> ★
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Your Review</label>
                    <textarea name="review_text" class="form-control" rows="5" required><?php echo htmlspecialchars($review['review_text']); ?></textarea>
                </div>

                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-1"></i>Update Review
                    </button>
                    <a href="../books/details.php?id=<?php echo $review['book_id']; ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
