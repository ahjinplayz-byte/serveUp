<?php

session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: checkout.php");
    exit();
}

$name = $_POST['name'];
$orderType = $_POST['orderType'];
$time = $_POST['time'];
$payment = $_POST['payment'];

if (!isset($_POST['cart'])) {
    die("Cart is missing.");
}

$cart = json_decode($_POST['cart'], true);

if (!is_array($cart) || count($cart) === 0) {
    die("Cart is empty.");
}


// Create order
$sql = "INSERT INTO customer_orders
        (name, orderType, time, payment, status)
        VALUES
        ('$name', '$orderType', '$time', '$payment', 'pending')";

if (!mysqli_query($conn, $sql)) {
    die("Order creation failed: " . mysqli_error($conn));
}


// Get the new order ID
$order_id = mysqli_insert_id($conn);


// Save every cart item
foreach ($cart as $item) {

    $product_name = $item['name'];
    $quantity = $item['quantity'];
    $price = $item['price'];

    $sql = "INSERT INTO order_items
            (order_id, product_name, quantity, price)
            VALUES
            ('$order_id', '$product_name', '$quantity', '$price')";

    if (!mysqli_query($conn, $sql)) {
        die("Item insertion failed: " . mysqli_error($conn));
    }
}


// Send customer to their order
header("Location: customer.php?id=" . $order_id);
exit();

?>