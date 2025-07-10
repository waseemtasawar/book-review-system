<?php
require_once('../config/db.php');
include('../includes/auth_session.php');

// Admin check
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Access denied. Admins only.</div></div>";
    exit();
}

// Get book ID
$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch existing book details
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

if (!$book) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Book not found.</div></div>";
    exit();
}

$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $genre = trim($_POST['genre']);
    $year = intval($_POST['publication_year']);
    $description = trim($_POST['description']);
    $cover_image = $book['cover_image'];

    // If a new cover image is uploaded
    if (!empty($_FILES['cover_image']['name'])) {
        $target_dir = "../assets/images/";
        $filename = basename($_FILES["cover_image"]["name"]);
        $target_file = $target_dir . $filename;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
                $cover_image = $filename;
            }
        }
    }

    $update = $conn->prepare("UPDATE books SET title = ?, author = ?, genre = ?, publication_year = ?, description = ?, cover_image = ? WHERE id = ?");
    $update->bind_param("ssssssi", $title, $author, $genre, $year, $description, $cover_image, $book_id);

    if ($update->execute()) {
        header("Location: manage_books.php");
        exit();
    } else {
        $message = "❌ Failed to update book.";
    }
}

include('../includes/header.php');
?>

<div class="container py-5">
    <h2 class="mb-4 fw-bold text-primary"><i class="fas fa-edit me-2"></i>Edit Book</h2>

    <?php if ($message): ?>
        <div class="alert alert-danger"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm border-0">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($book['title']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Author</label>
            <input type="text" name="author" class="form-control" value="<?php echo htmlspecialchars($book['author']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Genre</label>
            <input type="text" name="genre" class="form-control" value="<?php echo htmlspecialchars($book['genre']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Publication Year</label>
            <input type="number" name="publication_year" class="form-control" value="<?php echo htmlspecialchars($book['publication_year']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($book['description']); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Cover Image</label><br>
            <?php if (!empty($book['cover_image'])): ?>
                <img src="../assets/images/<?php echo $book['cover_image']; ?>" width="100" class="img-thumbnail mb-2"><br>
            <?php endif; ?>
            <input type="file" name="cover_image" class="form-control">
            <small class="text-muted">Optional. JPG, PNG, GIF</small>
        </div>

        <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Save Changes</button>
        <a href="manage_books.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
