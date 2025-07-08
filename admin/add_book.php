<?php
require_once('../config/db.php');
include('../includes/auth_session.php');
include('../includes/header.php');

if (!$_SESSION['is_admin']) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Access denied. Admins only.</div></div>";
    include('../includes/footer.php');
    exit();
}

// Fetch summary data
$total_users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$total_books = $conn->query("SELECT COUNT(*) FROM books")->fetch_row()[0];
$total_reviews = $conn->query("SELECT COUNT(*) FROM reviews")->fetch_row()[0];

// Fetch chart data
$chart_result = $conn->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS total
    FROM reviews
    GROUP BY month ORDER BY month
");

$months = $counts = [];
while ($row = $chart_result->fetch_assoc()) {
    $months[] = $row['month'];
    $counts[] = $row['total'];
}

// Recent users
$recent_users = $conn->query("SELECT name, email, created_at FROM users ORDER BY created_at DESC LIMIT 5");
?>

<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary"><i class="fas fa-cogs me-2"></i>Admin Dashboard</h2>
        <span class="text-muted"><i class="far fa-clock me-1"></i><span id="clock"></span></span>
    </div>

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <?php
        $stats = [
            ['icon' => 'fa-users', 'label' => 'Users', 'value' => $total_users, 'color' => 'primary'],
            ['icon' => 'fa-book', 'label' => 'Books', 'value' => $total_books, 'color' => 'success'],
            ['icon' => 'fa-star', 'label' => 'Reviews', 'value' => $total_reviews, 'color' => 'warning'],
        ];
        foreach ($stats as $stat): ?>
            <div class="col-md-4">
                <div class="card text-center shadow border-0">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas <?= $stat['icon']; ?> fa-2x text-white bg-<?= $stat['color']; ?> p-3 rounded-circle shadow-sm"></i>
                        </div>
                        <h4 class="fw-bold"><?= $stat['value']; ?></h4>
                        <p class="text-muted"><?= $stat['label']; ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Admin Tools -->
    <div class="d-flex flex-wrap gap-3 mb-5">
        <a href="manage_books.php" class="btn btn-outline-primary"><i class="fas fa-book me-1"></i>Manage Books</a>
        <a href="add_book.php" class="btn btn-outline-success"><i class="fas fa-plus me-1"></i>Add Book</a>
        <a href="manage_users.php" class="btn btn-outline-secondary"><i class="fas fa-users-cog me-1"></i>Manage Users</a>
        <a href="moderate_reviews.php" class="btn btn-outline-warning"><i class="fas fa-comments me-1"></i>Moderate Reviews</a>
    </div>

    <!-- Chart + Activity -->
    <div class="row g-4 mb-5">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-chart-line me-2"></i>Monthly Review Activity</h5>
                    <canvas id="reviewChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-user-clock me-2"></i>Recent Users</h5>
                    <ul class="list-group list-group-flush small">
                        <?php while ($u = $recent_users->fetch_assoc()): ?>
                            <li class="list-group-item">
                                <strong><?= htmlspecialchars($u['name']); ?></strong><br>
                                <span class="text-muted"><?= $u['email']; ?> <br>
                                <small><?= $u['created_at']; ?></small></span>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Notification -->
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-envelope me-2"></i>Send Email Notification</h5>
            <form method="POST" action="../send_notification.php" class="row g-3">
                <div class="col-md-3">
                    <input type="number" name="user_id" class="form-control" placeholder="User ID" required>
                </div>
                <div class="col-md-4">
                    <input type="text" name="subject" class="form-control" placeholder="Subject" required>
                </div>
                <div class="col-md-5">
                    <input type="text" name="message" class="form-control" placeholder="Message" required>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-paper-plane me-1"></i>Send</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('reviewChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($months); ?>,
        datasets: [{
            label: 'Reviews',
            data: <?= json_encode($counts); ?>,
            backgroundColor: 'rgba(78, 115, 223, 0.5)',
            borderColor: 'rgba(78, 115, 223, 1)',
            borderWidth: 2
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        },
        plugins: {
            legend: { display: false }
        }
    }
});
</script>

<!-- Live Clock -->
<script>
function updateClock() {
    const now = new Date();
    document.getElementById("clock").innerText = now.toLocaleString();
}
setInterval(updateClock, 1000);
updateClock();
</script>

<?php include('../includes/footer.php'); ?>
