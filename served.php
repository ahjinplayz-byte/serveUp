<?php

include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit();
}

$id = $_GET['id'];

$sql = "UPDATE customer_orders
        SET status = 'completed'
        WHERE order_id = '$id'";

if (!mysqli_query($conn, $sql)) {
    die("Update failed: " . mysqli_error($conn));
}

header("Location: admin.php");
exit();

?>