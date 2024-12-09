<?php
session_start();
include 'db.php'; // Include your database connection file

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = isset($_POST['role']) && $_POST['role'] === 'admin' ? 'admin' : 'user'; // Default to user

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Prepare and execute the SQL query
    $query = $conn->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");

    // Use execute with an array of parameters
    if ($query->execute([$name, $email, $password_hash, $role])) {
        // Registration successful
        $_SESSION['user_id'] = $conn->lastInsertId(); // Get the last inserted ID
        $_SESSION['role'] = $role;

        // Redirect to the appropriate dashboard
        if (isset($_SESSION['user_id'])) {
            if ($role === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        }
    } else {
        echo "Registration failed: " . $query->errorInfo()[2]; // Display error message
    }
}
?>
