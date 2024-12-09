<?php
session_start();
include 'db.php';

// Ensure that the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not an admin
    exit();
}

// Check if an action was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the user ID and the action (suspend or delete)
    $user_id = $_POST['user_id'];
    $action = $_POST['action'];

    // Handle suspension or deletion
    if ($action === 'suspend') {
        // Suspend the user by updating their status in the database
        $stmt = $conn->prepare("UPDATE users SET status = 'suspended' WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Optional: Add a log or a notification system here if needed
        $_SESSION['message'] = "User with ID $user_id has been suspended.";
    } elseif ($action === 'delete') {
        // Delete the user from the database
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Optional: Add a log or a notification system here if needed
        $_SESSION['message'] = "User with ID $user_id has been deleted.";
    }

    // Handle transaction actions (complete or cancel)
    if (isset($_POST['transaction_id'])) {
        $transaction_id = $_POST['transaction_id'];

        if ($action === 'complete') {
            $stmt = $conn->prepare("UPDATE transactions SET status = 'completed' WHERE transaction_id = ?");
            $stmt->execute([$transaction_id]);
            $_SESSION['message'] = "Transaction $transaction_id has been marked as completed.";
        } elseif ($action === 'cancel') {
            $stmt = $conn->prepare("UPDATE transactions SET status = 'canceled' WHERE transaction_id = ?");
            $stmt->execute([$transaction_id]);
            $_SESSION['message'] = "Transaction $transaction_id has been canceled.";
        }
    }

    // Redirect back to the admin dashboard after performing the action
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!-- Optionally, add a confirmation form to confirm the action -->
<form method="post">
    <label for="user_id">User ID: </label>
    <input type="text" name="user_id" required><br>

    <label for="action">Action: </label>
    <select name="action">
        <option value="suspend">Suspend</option>
        <option value="delete">Delete</option>
    </select><br>

    <button type="submit">Submit</button>
</form>
