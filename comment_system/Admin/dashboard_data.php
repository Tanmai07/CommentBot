<?php
// Database configuration
$host = 'localhost';
$db_name = 'comments_db';
$db_user = 'root';
$db_pass = '';

// Create a connection to the database
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the total number of comments
$total_comments_query = "SELECT COUNT(*) AS total_comments FROM comments";
$total_comments_result = $conn->query($total_comments_query);
$total_comments = 0;
if ($total_comments_result->num_rows > 0) {
    $row = $total_comments_result->fetch_assoc();
    $total_comments = $row['total_comments'];
}

// Fetch the total number of deleted vulgar comments
$deleted_comments_query = "SELECT COUNT(*) AS total_deleted_comments FROM deleted_comments";
$deleted_comments_result = $conn->query($deleted_comments_query);
$total_deleted_comments = 0;
if ($deleted_comments_result->num_rows > 0) {
    $row = $deleted_comments_result->fetch_assoc();
    $total_deleted_comments = $row['total_deleted_comments'];
}

// Close the connection
$conn->close();

// Output the counts as JSON
echo json_encode([
    'total_comments' => $total_comments,
    'total_deleted_comments' => $total_deleted_comments
]);
?>
