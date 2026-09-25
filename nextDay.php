<?php

session_start();
include 'db.php';

if (!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) {
    header("Location: index.php");
    exit();
}

// Delete all order items first
$sql1 = "DELETE FROM order_items";

if (!mysqli_query($conn, $sql1)) {
    die("Failed to clear order items: " . mysqli_error($conn));
}

// Delete all customer orders
$sql2 = "DELETE FROM customer_orders";

if (!mysqli_query($conn, $sql2)) {
    die("Failed to clear orders: " . mysqli_error($conn));
}

// Return to admin dashboard
header("Location: admin.php");
exit();

?>