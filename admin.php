<?php
session_start();
if (!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) {
    header("Location: index.php");
    exit();
}

include 'db.php';

$result = mysqli_query($conn, 
"SELECT *
FROM customer_orders
WHERE status IN ('pending', 'preparing', 'ready')
ORDER BY order_id ASC");

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

//----------Pending Orders------------//
$pendingResult = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM customer_orders
     WHERE status = 'pending'"
);

$pendingRow = mysqli_fetch_assoc($pendingResult);

$pending = $pendingRow['total'];
//------------Total Orders-------------//
$totalResult = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM customer_orders"
);

$totalRow = mysqli_fetch_assoc($totalResult);

$total = $totalRow['total'];
//--------Completed Orders------------//
$completedResult = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM customer_orders
     WHERE status = 'completed'"
);

$completedRow = mysqli_fetch_assoc($completedResult);

$completed = $completedRow['total'];

?>
<!DOCTYPE html>
    <head>
        <meta http-equiv="refresh" content="5">
        <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Caacupe+One&display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="CSS\readStyle.css">
    </head>


    <body>
        <p id="title">DashBoard</p>
<!----------------------TABLE COUNTER--------------------------->
    <div class="con">
        <div class="orderTracker box1"><p>Pending Orders: <span id="pending"><?= $pending ?></span></p></div>
        <div class="orderTracker box2"><p>Completed Orders: <span id="completed"><?= $completed ?></span></p></div>
        <div class="orderTracker box3"><p>Total Orders: <span id="total"><?= $total ?></span></p></div>
    </div>
<!-------------------------------------------------------------->
<!------------------SIDEBAR-------------------->
        <div class="sidebar">
<!----image---->
<div class="profile-container">
    <div id="profilePicture" class="profile-picture">
        <img src="logo.png">
    </div>
</div>
<!-------------->
            <p id="username">Welcome</p>
            <a href="admin.php" id="">Dashboard</a>
            <a href="orderRecords.php" id="record">Order Records</a>
            <a href="expenses.php" id="">Expense Report</a>
            <a id="showNavbar">Product Management</a>
            <br><br><br><br><br>
            <a href="logout.php">Logout</a>
</div>
<div id="productNavbar">
    <a href="staff.php">Edit Highlights</a>
    <a href="MenuStaff.php">Edit Menu</a>
</div>
<!----------------------------------------------------------->
<!-------------ORDER TABLE--------------->
        <p id="side">Orders:</p>
<table id="myTable">
    <tr>
        <th>ID</th> 
        <th>Items</th>
        <th>Name</th>
        <th>Order Type</th>
        <th>Time</th>
        <th>Payment Method</th>
        <th>Actions</th>
    </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

<tr>
    <td><?= htmlspecialchars($row['order_id']) ?></td>
    
    <td>
    <?php
    $itemResult = mysqli_query(
    $conn,
    "SELECT *
     FROM order_items
     WHERE order_id = '{$row['order_id']}'"
    );
    while ($item = mysqli_fetch_assoc($itemResult)) {
    ?>
    <?= htmlspecialchars($item['product_name']) ?>
    × <?= htmlspecialchars($item['quantity']) ?>
    <br>
    <?php } ?>
    </td>

    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['orderType']) ?></td>
    <td><?= htmlspecialchars($row['time']) ?></td>
    <td><?= htmlspecialchars($row['payment']) ?></td>

    <td>

<?php if ($row['status'] === 'pending'): ?>
    <a href="accept.php?id=<?= $row['order_id'] ?>"
       class="btn btn-accept">
        Accept
    </a>
    <a href="reject.php?id=<?= $row['order_id'] ?>"
       class="btn btn-reject">
        Reject
    </a>
<?php elseif ($row['status'] === 'preparing'): ?>
    <a href="done.php?id=<?= $row['order_id'] ?>"
       class="btn btn-done">
        Done
    </a>
<?php elseif ($row['status'] === 'ready'): ?>

    <a href="served.php?id=<?= $row['order_id'] ?>"
       class="btn btn-served">
        Served
    </a>
<?php endif; ?>

</td>
</tr>

<?php } ?>
</table>



<script src="JS/admin.js">
</script>
    </body>
</html>


