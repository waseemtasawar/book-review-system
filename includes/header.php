<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Review System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Fonts and Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- AOS CSS -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="/book-review-system/assets/css/style.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .navbar {
            background: linear-gradient(90deg, #2c3e50, #4b6cb7);
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }

        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
        }

        .navbar-brand i {
            margin-right: 10px;
            color: #ffc107;
        }

        .nav-link {
            color: #e0e0e0 !important;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-link:hover,
        .dropdown-menu a:hover {
            color: #ffc107 !important;
        }

        .dropdown-menu {
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .btn-login {
            margin-left: 10px;
            color: #fff;
            border: 1px solid #ffc107;
        }

        .btn-register {
            margin-left: 10px;
            background-color: #ffc107;
            color: #000;
            border: none;
        }

        .btn-login:hover,
        .btn-register:hover {
            opacity: 0.9;
        }

        .dropdown-toggle img {
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

 <nav class="navbar navbar-expand-lg shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="<?php
            echo (isset($_SESSION['user_id']) && !empty($_SESSION['is_admin']))
                ? '/book-review-system/admin/dashboard.php'
                : '/book-review-system/index.php';
        ?>">
            <i class="fas fa-book-reader"></i> BookReview
        </a>

        <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/book-review-system/books/catalog.php"><i class="fas fa-book"></i> Books</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/book-review-system/about.php"><i class="fas fa-info-circle"></i> About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/book-review-system/contact.php"><i class="fas fa-envelope"></i> Contact</a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                       <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
    <?php if (!empty($_SESSION['profile_picture'])): ?>
        <img src="/book-review-system/assets/images/<?php echo htmlspecialchars($_SESSION['profile_picture'] ?? 'default.jpg'); ?>" alt="Avatar" width="32" height="32" class="me-2 rounded-circle" style="object-fit: cover; height: 32px;">
    <?php else: ?>
        <i class="fas fa-user-circle me-2 text-white fs-5"></i>
    <?php endif; ?>
    <?php echo htmlspecialchars($_SESSION['user_name']); ?>
</a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (!empty($_SESSION['is_admin'])): ?>
                                <li><a class="dropdown-item" href="/book-review-system/admin/dashboard.php"><i class="fas fa-tools"></i> Admin Panel</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="/book-review-system/user/profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                                <li><a class="dropdown-item" href="/book-review-system/user/wishlist.php"><i class="fas fa-heart"></i> Wishlist</a></li>
                                <li><a class="dropdown-item" href="/book-review-system/user/recommendations.php"><i class="fas fa-star"></i> Recommendations</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/book-review-system/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-sm btn-outline-warning btn-login" href="/book-review-system/auth/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-sm btn-register" href="/book-review-system/auth/register.php"><i class="fas fa-user-plus"></i> Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

 <div class="container mt-4">
<!-- AOS JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init();
</script>

 </body
