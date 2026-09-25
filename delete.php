<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM expenses WHERE id = '$id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: expenses.php");
        exit();
    } else {
        die("DELETE ERROR: " . mysqli_error($conn));
    }
} else {
    header("Location: expenses.php");
    exit();
}
?>