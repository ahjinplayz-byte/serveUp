<?php
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($conn)) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$sql = "SELECT * FROM highlights ORDER BY id ASC LIMIT 3";
$result = $conn->query($sql);

$highlights = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $highlights[] = $row;
    }
}

echo json_encode(['highlights' => $highlights]);
?><?php
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($conn)) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$sql = "SELECT * FROM highlights ORDER BY id ASC LIMIT 3";
$result = $conn->query($sql);

$highlights = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $highlights[] = $row;
    }
}

echo json_encode(['highlights' => $highlights]);
?>