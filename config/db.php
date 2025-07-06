<?php
$host = "localhost";
$db_user = "root";
$db_password = ""; // <== leave blank
$db_name = "book_review_system";

// Create connection
$conn = new mysqli($host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}
?>
