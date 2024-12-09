<?php
include 'db.php';

if (isset($_GET['partner_id'])) {
    $partner_id = $_GET['partner_id'];

    $stmt = $conn->prepare("SELECT item_id, name, value FROM items WHERE user_id = :partner_id AND status = 'available'");
    $stmt->bindParam(':partner_id', $_GET['partner_id']);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($items); 
} else {
    echo json_encode([]);
}
?>


