<?php
require_once('../config/db.php');
include('../includes/auth_session.php');
include('../includes/header.php');

if (!$_SESSION['is_admin']) {
    echo "<div class='alert alert-danger'>Access denied. Admins only.</div>";
    include('../includes/footer.php');
    exit();
}

$users = $conn->query("SELECT id, name, email, is_admin FROM users ORDER BY created_at DESC");
?>

<h2>Manage Users</h2>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Admin</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($user = $users->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo $user['is_admin'] ? 'Yes' : 'No'; ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include('../includes/footer.php'); ?>
