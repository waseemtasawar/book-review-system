<?php
require_once('../config/db.php');
include('../includes/auth_session.php');

$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating']);
    $review_text = trim($_POST['review_text']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO reviews (user_id, book_id, rating, review_text) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $user_id, $book_id, $rating, $review_text);

    if ($stmt->execute()) {
        // ✅ Safe to redirect BEFORE output
        header("Location: ../books/details.php?id=" . $book_id);
        exit();
    } else {
        $message = "❌ Failed to add review.";
    }
}

include('../includes/header.php');
?>

<div class="container py-5">
    <h3 class="mb-4 fw-bold text-success"><i class="fas fa-pen-nib me-2"></i>Add Your Review</h3>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-danger mb-3 d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="row g-3">
                <div class="col-12">
                    <label class="form-label">Rating</label>
                    <select name="rating" class="form-select" required>
                        <option value="">Select a rating</option>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?> ★</option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Your Review</label>
                    <textarea name="review_text" class="form-control" rows="5" placeholder="Share your thoughts..." required></textarea>
                </div>

                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-success me-2"><i class="fas fa-paper-plane me-1"></i>Submit Review</button>
                    <a href="../books/details.php?id=<?php echo $book_id; ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
