<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize user input
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; 

    // Validate the email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: login_page.html?message=Invalid email format.");
        exit;
    }

    // Connect to the database
    $conn = new mysqli("localhost", "root", "", "comments_db");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the SQL query
    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_password);
        $stmt->fetch();

        // Verify the password
        if ($password == $user_password) {
            $_SESSION['user_email'] = $email; // Store user email in session
            header("Location: index.html");
            exit;
        } else {
            header("Location: login_page.html?message=Invalid password.");
            exit;
        }
    } else {
        header("Location: login_page.html?message=No user found with that email.");
        exit;
    }

    $stmt->close();
    $conn->close();
}
?>
