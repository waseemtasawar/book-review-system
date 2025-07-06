<?php
require_once('../config/db.php');
include('../includes/auth_session.php');

$review_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

// Get book ID before deleting
$stmt = $conn->prepare("SELECT book_id FROM reviews WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $review_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "Review not found or unauthorized.";
    exit();
}

$book_id = $result->fetch_assoc()['book_id'];

// Delete review
$delete = $conn->prepare("DELETE FROM reviews WHERE id = ? AND user_id = ?");
$delete->bind_param("ii", $review_id, $user_id);
$delete->execute();

header("Location: ../books/details.php?id=" . $book_id);
exit();
?>
