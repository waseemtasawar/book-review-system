<?php
require_once('../config/db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

if (!isset($_POST['book_id'])) {
    die("Error: Book ID not provided.");
}

$user_id = intval($_SESSION['user_id']);
$book_id = intval($_POST['book_id']);

// Optional: validate that book exists
$checkBook = $conn->prepare("SELECT id FROM books WHERE id = ?");
$checkBook->bind_param("i", $book_id);
$checkBook->execute();
$bookResult = $checkBook->get_result();
if ($bookResult->num_rows === 0) {
    die("Error: Book does not exist.");
}

// Check if already in wishlist
$check = $conn->prepare("SELECT * FROM wishlists WHERE user_id = ? AND book_id = ?");
$check->bind_param("ii", $user_id, $book_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // Remove from wishlist
    $delete = $conn->prepare("DELETE FROM wishlists WHERE user_id = ? AND book_id = ?");
    $delete->bind_param("ii", $user_id, $book_id);
    $delete->execute();
} else {
    // Add to wishlist
    $insert = $conn->prepare("INSERT INTO wishlists (user_id, book_id) VALUES (?, ?)");
    $insert->bind_param("ii", $user_id, $book_id);
    $insert->execute();
}

header("Location: ../books/details.php?id=" . $book_id);
exit();
