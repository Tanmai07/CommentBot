<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize user input
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: registration_page.html?message=Invalid email format.");
        exit;
    }

    // Connect to the database
    $conn = new mysqli("localhost", "root", "", "comments_db");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the user already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        header("Location: registration_page.html?message=User already exists.");
        exit;
    }

    // Insert the new user into the database (without hashing the password for now)
    $stmt = $conn->prepare("INSERT INTO users (email, password, full_name) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $password, $full_name);
    
    if ($stmt->execute()) {
        header("Location: login_page.html?message=Registration successful. Please log in.");
    } else {
        header("Location: registration_page.html?message=Registration failed. Please try again.");
    }

    $stmt->close();
    $conn->close();
}
?>
