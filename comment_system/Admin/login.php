<?php
// Start the session
session_start();

// Database configuration
$host = 'localhost'; // Database host
$db_name = 'comments_db'; // Database name
$db_user = 'root'; // Database user
$db_pass = ''; // Database password

// Create a connection to the database
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the user input
    $admin_email = $_POST['admin_email'];
    $admin_password = $_POST['admin_password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT * FROM admin WHERE admin_email = ?");
    $stmt->bind_param("s", $admin_email);
    
    // Execute the statement
    $stmt->execute();
    
    // Store the result
    $result = $stmt->get_result();
    
    // Check if the email exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify the password (for simplicity, we are not hashing passwords)
        if ($admin_password == $user['admin_password']) {
            // Set session variables
            $_SESSION['admin_email'] = $user['admin_email'];
            
            // Redirect to the dashboard
            header("Location: CBDashboard.html");
            exit();
        } else {
            // Invalid password, redirect with error
            header("Location: CommentBot.html?error=Invalid password.");
            exit();
        }
    } else {
        // Email not found, redirect with error
        header("Location: CommentBot.html?error=Email not found.");
        exit();
    }
}

// Close the connection
$conn->close();
?>
