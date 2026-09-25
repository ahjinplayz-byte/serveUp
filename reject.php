<?php

include 'db.php';

$id = $_GET['id'];

$sql = "UPDATE customer_orders
        SET status = 'cancelled'
        WHERE order_id = '$id'";

mysqli_query($conn, $sql);

header("Location: admin.php");
exit();

?>