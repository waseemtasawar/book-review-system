<?php
require_once('../config/db.php');
include('../includes/auth_session.php');

$user_id = $_SESSION['user_id'];
$message = "";

// Get user info
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name  = trim($_POST["name"]);
    $phone = trim($_POST["phone"]);
    $bio   = trim($_POST["bio"]);

    // Handle image upload
    $profile_picture = $user['profile_picture'];
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "../assets/images/";
        $filename = time() . "_" . basename($_FILES["profile_picture"]["name"]);
        $target_file = $target_dir . $filename;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $profile_picture = $filename;
            } else {
                $message = "❌ Failed to upload profile picture.";
            }
        } else {
            $message = "❌ Invalid image format.";
        }
    }

    if (empty($message)) {
        $update = $conn->prepare("UPDATE users SET name = ?, phone = ?, bio = ?, profile_picture = ? WHERE id = ?");
        $update->bind_param("ssssi", $name, $phone, $bio, $profile_picture, $user_id);

        if ($update->execute()) {
            $_SESSION['user_name'] = $name;
            header("Location: profile.php");
            exit();
        } else {
            $message = "❌ Failed to update profile.";
        }
    }
}

include('../includes/header.php');
?>

<div class="container py-5">
    <h2 class="mb-4 fw-bold text-primary"><i class="fas fa-user-edit me-2"></i>Edit Profile</h2>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-danger d-flex align-items-center mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>">
                </div>

                <div class="col-12">
                    <label class="form-label">Bio</label>
                    <textarea name="bio" class="form-control" rows="4"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Profile Picture</label><br>
                    <?php if (!empty($user['profile_picture'])): ?>
                        <img src="/book-review-system/assets/images/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile" class="img-thumbnail mb-2" width="100">
                    <?php endif; ?>
                    <input type="file" name="profile_picture" class="form-control">
                    <small class="text-muted">Accepted formats: JPG, PNG, GIF</small>
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-success me-2"><i class="fas fa-save me-1"></i>Save Changes</button>
                    <a href="profile.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
