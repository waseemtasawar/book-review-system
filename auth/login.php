<?php
require_once('../config/db.php');

// ✅ Prevent duplicate session warnings
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();

        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["is_admin"] = $user["is_admin"];

            // ✅ Redirect to admin or user dashboard
            header("Location: " . ($user["is_admin"] ? "../admin/dashboard.php" : "../index.php"));
            exit();
        } else {
            $message = "❌ Incorrect password.";
        }
    } else {
        $message = "❌ No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Book Review System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Styles & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(90deg, #2c3e50, #4b6cb7);
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            border: none;
            border-radius: 1rem;
        }

        .form-control:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }

        .login-header {
            font-weight: 700;
            color: #333;
        }

        .small-link {
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg p-4 bg-white">
                <div class="text-center mb-4">
                    <h3 class="login-header"><i class="fas fa-sign-in-alt me-2 text-primary"></i>Login</h3>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label" for="email">Email address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" id="email" class="form-control" required placeholder="you@example.com" aria-label="Email">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••" aria-label="Password">
                        </div>
                    </div>

                    <div class="text-end mb-3">
                        <a href="/book-review-system/auth/forgot_password.php" class="text-decoration-none small-link text-muted">Forgot your password?</a>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning fw-semibold">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </button>
                    </div>
                </form>

                <div class="mt-4 text-center">
                    <small>Don't have an account? <a href="register.php" class="text-decoration-none">Register</a></small>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
