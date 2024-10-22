<?php
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

// Load keywords from the file
$keywords = file('vulgur_words_DS.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$keywordPattern = implode('|', array_map('preg_quote', $keywords));

// Query to get all comments and full name of the user using email
$sql = "SELECT comments.comment_text, comments.created_at, users.full_name, comments.email 
        FROM comments 
        JOIN users ON comments.email = users.email
        ORDER BY comments.created_at ASC";

$result = $conn->query($sql);

// Check if there are comments
if ($result->num_rows > 0) {
    // Output comments as HTML
    while ($row = $result->fetch_assoc()) {
        
        $comment = $row['comment_text'];
        $fullname = htmlspecialchars($row['full_name']); // Sanitize the full name
        $user_email = $row['email']; // Get the user email
        $created_at = $row['created_at'];

        if (preg_match("/($keywordPattern)/i", $comment)) {
            // Check if the comment already exists in the deleted_comments table
            $check_sql = "SELECT * FROM deleted_comments 
                          WHERE comment_text = '$comment' 
                          AND email = '$user_email' 
                          AND deleted_at = '$created_at'";
            $check_result = $conn->query($check_sql);
        
            // If it does not exist, insert the deleted comment
            if ($check_result->num_rows == 0) {
                // Insert the removed comment into the deleted_comments table
                $deleted_comment_text = $conn->real_escape_string($comment);
                $insert_sql = "INSERT INTO deleted_comments (comment_text, email, deleted_at, full_name) 
                               VALUES ('$deleted_comment_text', '$user_email', '$created_at','$fullname')";
                $conn->query($insert_sql); // Execute the insertion
            } else {
                // Optionally, you could log that the comment was already deleted
            }
        
            // Replace the comment with a removal notice
            $comment = "<span style='color: red;'>This comment was removed due to violation of communication rules.</span>"; 
        }

        echo "<div class='comment'>";
        echo "<div class='avatar'><i class='fas fa-user'></i><p><strong>" . $fullname . "</strong></p></div>"; // Placeholder for avatar
        
        // Wrap comment and time in a flex container
        echo "<div class='time' style='display: flex; justify-content: space-between; align-items: center;'>";
        echo "<p class='main_comment' style='margin: 0;'>" . $comment . "</p>";
        echo "<small style='margin-left: 20px;'>Posted on: " . htmlspecialchars($row['created_at']) . "</small>";
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<p style='color: white; display: flex; align-items: center; justify-content: center; height: 100px; width: 100%;'>No comments yet. Be the first to comment!</p>";
}

// Close connection
$conn->close();
?>
