<?php

session_start();
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: checkout.php");
    exit();
}

$order_id = $_GET['id'];

$sql = "SELECT *
        FROM customer_orders
        WHERE order_id = '$order_id'";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$order = mysqli_fetch_assoc($result);

if (!$order) {
    die("Order not found.");
}


// Decide which dashboard to show
switch ($order['status']) {

    case 'pending':
        header("Location: dashboardOne.php?id=" . $order_id);
        break;

    case 'preparing':
        header("Location: dashboardTwo.php?id=" . $order_id);
        break;

    case 'ready':
        header("Location: dashboardThree.php?id=" . $order_id);
        break;

    case 'cancelled':
    case 'completed':
        header("Location: orderRecords.php?id=" . $order_id);
        break;

    default:
        header("Location: checkout.php");
        break;
}

exit();

?>