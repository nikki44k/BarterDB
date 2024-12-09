<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item1 = trim($_POST['item1']);
    $item2 = trim($_POST['item2']);
    $equivalent_value = $_POST['equivalent_value'];

    $query = $conn->prepare("INSERT INTO equivalence (item1, item2, equivalent_value) VALUES (?, ?, ?)");
    $query->bind_param("ssd", $item1, $item2, $equivalent_value);

    if ($query->execute()) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Failed to add equivalence: " . $query->error;
    }
}
?>
