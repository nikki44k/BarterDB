<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id1 = $_POST['item_id1']; // The ID of the item the user is offering
    $partner_id = $_POST['partner_id']; // The ID of the user they want to trade with
    $item_id2 = $_POST['item_id2']; // The ID of the item they want to receive

    // Validate if partner_id exists in the users table
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE user_id = ?");
    $stmt->execute([$partner_id]);
    if ($stmt->rowCount() == 0) {
        echo "Invalid Partner ID. Please enter a valid user ID.";
        exit();
    }

    // Check if both items exist
    $stmt = $pdo->prepare("SELECT * FROM items WHERE item_id IN (?, ?)");
    $stmt->execute([$item_id1, $item_id2]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($items) == 2) {
        // Generate a 16-character hash key
        $hash_key = substr(md5(uniqid(rand(), true)), 0, 16);

        // Record the transaction in the database
        $stmt = $pdo->prepare("INSERT INTO transactions (item_id, user_id, partner_id, hash_key) VALUES (?, ?, ?, ?)");
        $stmt->execute([$item_id1, $_SESSION['user_id'], $partner_id, $hash_key]);

        echo "Trade initiated successfully. Hash Key: $hash_key";
    } else {
        echo "One or both items do not exist.";
    }
}
?>

<form action="trade.php" method="post">
    <input type="hidden" name="item_id1" value="1"> <!-- Assume user is offering item with ID 1 -->
    <input type="number" name="partner_id" placeholder="Partner User ID" required>
    <input type="hidden" name="item_id2" value="2"> <!-- Assume user wants item with ID 2 -->
    <button type="submit">Initiate Trade</button>
</form>
