<?php
require_once('../config/db.php');
include('../includes/auth_session.php');
include('../includes/header.php');

if (!$_SESSION['is_admin']) {
    echo "<div class='alert alert-danger'>Access denied. Admins only.</div>";
    include('../includes/footer.php');
    exit();
}

$books = $conn->query("SELECT * FROM books ORDER BY created_at DESC");
?>

<h2>Manage Books</h2>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Cover</th>
            <th>Title</th>
            <th>Author</th>
            <th>Genre</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($book = $books->fetch_assoc()): ?>
            <tr>
                <td><img src="../assets/images/<?php echo $book['cover_image']; ?>" width="60"></td>
                <td><?php echo htmlspecialchars($book['title']); ?></td>
                <td><?php echo htmlspecialchars($book['author']); ?></td>
                <td><?php echo htmlspecialchars($book['genre']); ?></td>
                <td>
                    <!-- Implement edit and delete later -->
                    <a href="#" class="btn btn-sm btn-outline-primary disabled">Edit</a>
                    <a href="#" class="btn btn-sm btn-outline-danger disabled">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include('../includes/footer.php'); ?>
