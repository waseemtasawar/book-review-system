<?php
require_once('../config/db.php');
include('../includes/auth_session.php');
include('../includes/header.php');

if (!$_SESSION['is_admin']) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Access denied. Admins only.</div></div>";
    include('../includes/footer.php');
    exit();
}

$users = $conn->query("SELECT id, name, email, is_admin FROM users ORDER BY created_at DESC");
?>

<div class="container py-5">
    <h2 class="mb-4 fw-bold text-primary"><i class="fas fa-users-cog me-2"></i>Manage Users</h2>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="badge <?php echo $user['is_admin'] ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo $user['is_admin'] ? 'Yes' : 'No'; ?>
                                    </span>
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
