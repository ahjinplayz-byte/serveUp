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


// Status changed?
if ($order['status'] === 'pending') {
    header("Location: dashboardOne.php?id=" . $order_id);
    exit();
}

if ($order['status'] === 'preparing') {
    header("Location: dashboardTwo.php?id=" . $order_id);
    exit();
}

if ($order['status'] === 'completed') {
    header("Location: orderRecords.php?id=" . $order_id);
    exit();
}

if ($order['status'] === 'cancelled') {
    header("Location: orderRecords.php?id=" . $order_id);
    exit();
}

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5">
        <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Caacupe+One&display=swap" rel="stylesheet">
    <style>
p{
    text-align:center;
    margin-bottom:100px;
    margin-top:200px;
    font-family: sans-serif;
    font-weight: bold;
    font-size: 50px;
    color: #3b2a22;
}
button{
    border-radius: 5px;
    text-align:center;
    font-family: sans-serif;
    font-weight: bold;
    font-size: 20px;
    color: #3b2a22;
    background: #f3e9d7;
    margin-left:680px;
}
button:hover{
    font-weight: bold;
    font-size: 20px;
    background: #3b2a22;
    color: #f3e9d7;
}
a{
    text-decoration: none;
    color: #3b2a22;
}
a:hover{
    color: #f3e9d7;
}
    </style>
    <title>Order Ready</title>
  </head>

  <body>
    <p>Your Order is Ready!</p>
    <a href="orderReceived.php?id=<?= $order['order_id'] ?>"><button type="button">Order Received</button></a>
</body>