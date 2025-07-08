<?php
require_once('../config/db.php');
include('../includes/auth_session.php');
include('../includes/header.php');

if (!$_SESSION['is_admin']) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Access denied. Admins only.</div></div>";
    include('../includes/footer.php');
    exit();
}

$total_users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$total_books = $conn->query("SELECT COUNT(*) FROM books")->fetch_row()[0];
$total_reviews = $conn->query("SELECT COUNT(*) FROM reviews")->fetch_row()[0];

// Fetch recent users
$recent_users = $conn->query("SELECT name, email, created_at FROM users ORDER BY created_at DESC LIMIT 5");

// Fetch review counts by month
$chart_data = $conn->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count
    FROM reviews
    GROUP BY month
    ORDER BY month ASC
");

$months = [];
$counts = [];
while ($row = $chart_data->fetch_assoc()) {
    $months[] = $row['month'];
    $counts[] = $row['count'];
}
?>

<div class="container py-5">
    <!-- Header -->
    <!-- Header -->
<div class="p-4 rounded shadow-sm mb-5 text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
    <h2 class="fw-bold"><i class="fas fa-tools me-2"></i>Admin Dashboard</h2>
    <p class="mb-0">Monitor and manage all activities across the platform.</p>
</div>


    <!-- Stat Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <i class="fas fa-users fa-2x text-white bg-primary p-3 rounded-circle mb-3"></i>
                    <h4><?php echo $total_users; ?></h4>
                    <p class="text-muted">Total Users</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <i class="fas fa-book fa-2x text-white bg-success p-3 rounded-circle mb-3"></i>
                    <h4><?php echo $total_books; ?></h4>
                    <p class="text-muted">Total Books</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <i class="fas fa-star fa-2x text-white bg-warning p-3 rounded-circle mb-3"></i>
                    <h4><?php echo $total_reviews; ?></h4>
                    <p class="text-muted">Total Reviews</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Actions -->
    <div class="row g-3 mb-5">
        <div class="col-md-3 col-sm-6">
            <a href="manage_books.php" class="btn btn-outline-primary w-100"><i class="fas fa-book me-1"></i>Manage Books</a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="add_book.php" class="btn btn-outline-success w-100"><i class="fas fa-plus me-1"></i>Add Book</a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="manage_users.php" class="btn btn-outline-secondary w-100"><i class="fas fa-users-cog me-1"></i>Manage Users</a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="moderate_reviews.php" class="btn btn-outline-warning w-100"><i class="fas fa-comments me-1"></i>Moderate Reviews</a>
        </div>
    </div>

    <!-- Chart & Activity -->
    <div class="row g-4 mb-5">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-chart-line me-2"></i>Monthly Review Activity</h5>
                    <canvas id="reviewChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-clock me-2"></i>Recent Users</h5>
                    <ul class="list-group list-group-flush">
                        <?php while ($user = $recent_users->fetch_assoc()): ?>
                            <li class="list-group-item small">
                                <strong><?php echo htmlspecialchars($user['name']); ?></strong><br>
                                <span class="text-muted"><?php echo $user['email']; ?> <br>
                                <small><?php echo $user['created_at']; ?></small></span>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Message Placeholder -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
        <div id="dashboardToast" class="toast align-items-center text-white bg-success border-0" role="alert" data-bs-delay="3000">
            <div class="d-flex">
                <div class="toast-body">✅ Dashboard loaded successfully</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script>
    const ctx = document.getElementById('reviewChart').getContext('2d');
    const reviewChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'Reviews per Month',
                data: <?php echo json_encode($counts); ?>,
                backgroundColor: 'rgba(78, 84, 200, 0.2)',
                borderColor: '#4e54c8',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    // Show toast
    const toastEl = document.getElementById('dashboardToast');
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
</script>

<?php include('../includes/footer.php'); ?>
