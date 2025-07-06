<?php
require_once('../config/db.php');
include('../includes/auth_session.php');
include('../includes/header.php');

$user_id = $_SESSION['user_id'];
$message = "";

// Get user info
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $bio = trim($_POST["bio"]);

    $update = $conn->prepare("UPDATE users SET name = ?, bio = ? WHERE id = ?");
    $update->bind_param("ssi", $name, $bio, $user_id);

    if ($update->execute()) {
        $_SESSION['user_name'] = $name;
        header("Location: profile.php");
        exit();
    } else {
        $message = "❌ Failed to update profile.";
    }
}
?>

<h2>Edit Profile</h2>

<?php if ($message): ?>
    <div class="alert alert-danger"><?php echo $message; ?></div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Bio</label>
        <textarea name="bio" class="form-control" rows="4"><?php echo htmlspecialchars($user['bio']); ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Save Changes</button>
    <a href="profile.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include('../includes/footer.php'); ?>
