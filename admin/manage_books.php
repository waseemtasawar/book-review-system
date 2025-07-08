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
                                    <img src="../assets/images/<?php echo htmlspecialchars($book['cover_image']); ?>" width="60" class="rounded shadow-sm" alt="Cover">
                                </td>
                                <td><?php echo htmlspecialchars($book['title']); ?></td>
                                <td><?php echo htmlspecialchars($book['author']); ?></td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($book['genre']); ?></span></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="#" class="btn btn-sm btn-outline-primary disabled"><i class="fas fa-edit me-1"></i>Edit</a>
                                        <a href="#" class="btn btn-sm btn-outline-danger disabled"><i class="fas fa-trash me-1"></i>Delete</a>
                                    </div>
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
