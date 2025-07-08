<?php
require_once('../config/db.php');
include('../includes/auth_session.php');
include('../includes/header.php');

if (!$_SESSION['is_admin']) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Access denied. Admins only.</div></div>";
    include('../includes/footer.php');
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"]);
    $author = trim($_POST["author"]);
    $genre = trim($_POST["genre"]);
    $description = trim($_POST["description"]);
    $year = intval($_POST["publication_year"]);

    // Handle image upload
    $cover_image = "";
    if (!empty($_FILES['cover_image']['name'])) {
        $target_dir = "../assets/images/";
        $cover_image = basename($_FILES["cover_image"]["name"]);
        $target_file = $target_dir . $cover_image;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $valid_types = ["jpg", "jpeg", "png", "gif"];
        if (in_array($imageFileType, $valid_types)) {
            if (!move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
                $message = "⚠️ Failed to upload image.";
                $cover_image = "";
            }
        } else {
            $message = "⚠️ Invalid image format. Only JPG, PNG, GIF allowed.";
            $cover_image = "";
        }
    }

    // Insert book
    $stmt = $conn->prepare("INSERT INTO books (title, author, genre, description, publication_year, cover_image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssis", $title, $author, $genre, $description, $year, $cover_image);

    if ($stmt->execute()) {
        $message = "✅ Book added successfully!";
    } else {
        $message = "❌ Failed to add book.";
    }
}
?>

<div class="container py-5">
    <h2 class="mb-4 fw-bold text-success"><i class="fas fa-plus me-2"></i>Add New Book</h2>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo str_contains($message, '✅') ? 'success' : 'warning'; ?> d-flex align-items-center mb-4">
                    <i class="fas <?php echo str_contains($message, '✅') ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> me-2"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Author</label>
                    <input type="text" name="author" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Genre</label>
                    <input type="text" name="genre" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Publication Year</label>
                    <input type="number" name="publication_year" class="form-control" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="4" class="form-control" required></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Upload Cover Image</label>
                    <input type="file" name="cover_image" class="form-control" accept=".jpg,.jpeg,.png,.gif">
                    <small class="text-muted">Accepted formats: JPG, PNG, GIF</small>
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-success me-2"><i class="fas fa-save me-1"></i> Add Book</button>
                    <a href="dashboard.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back to Dashboard</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
