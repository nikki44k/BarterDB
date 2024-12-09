<?php
session_start();
require 'db.php';

// Fetch all available items
$stmt = $pdo->query("SELECT * FROM items WHERE status = 'available'");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Available Items</h2>
<ul>
    <?php foreach ($items as $item): ?>
        <li>
            <strong><?= htmlspecialchars($item['name']) ?></strong><br>
            <?= htmlspecialchars($item['description']) ?><br>
            Quantity: <?= htmlspecialchars($item['quantity']) ?><br>
            <form action="match_items.php" method="post">
                <input type="hidden" name="item_id1" value="<?= $item['item_id'] ?>">
                <input type="text" name="partner_id" placeholder="Partner User ID" required>
                <button type="submit">Initiate Trade</button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>
