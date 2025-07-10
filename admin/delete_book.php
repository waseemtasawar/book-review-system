<?php
require_once('../config/db.php');
session_start();

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    die("Access denied.");
}

$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($book_id > 0) {
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    if ($stmt->execute()) {
        header("Location: manage_books.php");
        exit();
    } else {
        echo "❌ Failed to delete book.";
    }
} else {
    echo "Invalid book ID.";
}
