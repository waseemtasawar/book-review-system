<?php
require_once('../config/db.php');
include('../includes/auth_session.php');
include('../includes/header.php');

if (!$_SESSION['is_admin']) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Access denied. Admins only.</div></div>";
    include('../includes/footer.php');
    exit();
}

$books = $conn->query("SELECT * FROM books ORDER BY created_at DESC");
?>

<div class="container py-5">
    <h2 class="mb-4 fw-bold text-primary"><i class="fas fa-book me-2"></i>Manage Books</h2>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <?php if ($books->num_rows === 0): ?>
                <div class="alert alert-info">No books found.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Cover</th>
                                <th scope="col">Title</th>
                                <th scope="col">Author</th>
                                <th scope="col">Genre</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($book = $books->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($book['cover_image'])): ?>
                                            <img src="../assets/images/<?php echo htmlspecialchars($book['cover_image']); ?>" width="60" class="rounded shadow-sm" alt="Cover">
                                        <?php else: ?>
                                            <span class="text-muted">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($book['genre']); ?></span></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </a>
                                            <a href="delete_book.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this book?');">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
