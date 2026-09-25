<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header('Location: index.php');
    exit();
}

header('Location: saveRecord.php');
exit();
?>
