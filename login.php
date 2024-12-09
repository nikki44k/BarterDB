<?php
session_start();
include 'db.php'; // Include your database connection file

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $usertype = $_POST['usertype'];

    // Check if connection is successful before querying
    if ($conn) {
        // Prepare SQL statement to prevent SQL injection
        $query = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $query->bindParam(':email', $email); // Bind the email parameter
        $query->execute(); // Execute the query
        $user = $query->fetch(PDO::FETCH_ASSOC); // Fetch the result as an associative array

        // Verify password and user type, then log in
        if ($user && password_verify($password, $user['password_hash']) && $user['role'] === $usertype) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];

            // Redirect to the appropriate dashboard
            if ($_SESSION['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            echo "Invalid email, password, or user type.";
        }
    } else {
        echo "Database connection failed.";
    }
}
?>
