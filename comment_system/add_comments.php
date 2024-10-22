<?php
// Start session
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root"; // Default for XAMPP
$password = "";     // Default for XAMPP (no password)
$dbname = "comments_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the comment text and sanitize it
    $comment_text = $conn->real_escape_string($_POST['comment_text']);
    
    // Get the email from session (assume the user is logged in and email is stored in session)
    if (isset($_SESSION['user_email'])) {
        $user_email = $conn->real_escape_string($_SESSION['user_email']);
        
        // SQL query to insert the comment with the user's email
        $sql = "INSERT INTO comments (comment_text, email) VALUES ('$comment_text', '$user_email')";
        
        // Execute the query and return response
        if ($conn->query($sql) === TRUE) {
            echo "Comment added successfully!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error: User is not logged in. Email is not available.";
    }
}

// Close connection
$conn->close();
?>
