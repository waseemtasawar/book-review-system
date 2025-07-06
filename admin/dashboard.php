<?php
require_once('../config/db.php');
include('../includes/auth_session.php');
include('../includes/header.php');

// Only admin access
if (!$_SESSION['is_admin']) {
    echo "<div class='alert alert-danger'>Access denied. Admins only.</div>";
    include('../includes/footer.php');
    exit();
}

// Fetch totals
$total_users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$total_books = $conn->query("SELECT COUNT(*) FROM books")->fetch_row()[0];
$total_reviews = $conn->query("SELECT COUNT(*) FROM reviews")->fetch_row()[0];
?>

<h2>Admin Dashboard</h2>
<div class="row text-center">
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h4><?php echo $total_users; ?></h4>
                <p>Users</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h4><?php echo $total_books; ?></h4>
                <p>Books</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h4><?php echo $total_reviews; ?></h4>
                <p>Reviews</p>
            </div>
        </div>
    </div>
</div>

<a href="manage_books.php" class="btn btn-primary me-2">Manage Books</a>
<a href="manage_users.php" class="btn btn-secondary me-2">Manage Users</a>
<a href="moderate_reviews.php" class="btn btn-warning">Moderate Reviews</a>

<?php include('../includes/footer.php'); ?>
