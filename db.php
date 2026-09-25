<?php

$conn = mysqli_connect("localhost", "root", "", "serveUp");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

?>

