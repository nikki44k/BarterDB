<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction_id = $_POST['transaction_id'];
    $action = $_POST['action'];

    // Fetch transaction details
    $stmt = $conn->prepare("SELECT * FROM transactions WHERE transaction_id = :transaction_id");
    $stmt->bindParam(':transaction_id', $transaction_id, PDO::PARAM_INT);
    $stmt->execute();
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transaction) {
        die('Transaction not found.');
    }

    $user_id = $_SESSION['user_id'];

    // Ensure the logged-in user is the partner
    if ($transaction['partner_id'] != $user_id) {
        die('You are not authorized to perform this action.');
    }

    if ($action === 'accept') {
        // Update transaction status to 'completed'
        $stmt = $conn->prepare("UPDATE transactions SET status = 'completed', end_date = NOW() WHERE transaction_id = :transaction_id");
        $stmt->bindParam(':transaction_id', $transaction_id, PDO::PARAM_INT);
        $stmt->execute();

        // Optionally, update item statuses if needed
        $stmt = $conn->prepare("UPDATE items SET status = 'traded' WHERE item_id IN (:item_id)");
        $stmt->bindParam(':item_id', $transaction['item_id'], PDO::PARAM_INT);
        $stmt->execute();

    } elseif ($action === 'decline') {
        $stmt = $conn->prepare("UPDATE items SET status = 'available' WHERE item_id = :item_id");
        $stmt->bindParam(':item_id', $item_a_id);
        $stmt->execute();
    
        $stmt->bindParam(':item_id', $item_b_id);
        $stmt->execute();
    
        // Delete the transaction
        $stmt = $conn->prepare("DELETE FROM transactions WHERE transaction_id = :transaction_id");
        $stmt->bindParam(':transaction_id', $transaction_id);
        $stmt->execute();        
    }

    // Redirect back to the dashboard
    header("Location: user_dashboard.php");
    exit();
}
?>
