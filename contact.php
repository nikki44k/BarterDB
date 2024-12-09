<?php
// contact.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    try {
        // Database connection (use your credentials)
        $dsn = 'mysql:host=localhost;dbname=barterdb;charset=utf8';
        $pdo = new PDO($dsn, 'root', '');

        // Prepare SQL statement
        $sql = "INSERT INTO contact_messages (name, email, message) VALUES (:name, :email, :message)";
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);

        // Execute statement
        if ($stmt->execute()) {
            echo "Thank you for reaching out. Your message has been received.";
        } else {
            echo "Failed to send your message. Please try again.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    // Redirect to contact form if the script is accessed without a POST request
    header("Location: contact.html");
    exit();
}
?>
