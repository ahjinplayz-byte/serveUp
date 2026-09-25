<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and display submitted data
    echo "<h3>Submitted Data:</h3>";
    echo "<pre>";
    print_r($_POST['table_data']);
    echo "</pre>";
}
?>
<!DOCTYPE html>
    <head>
        <meta charset="UTF-8">
        <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Caacupe+One&display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="CSS\readStyle.css">
<style>
        .card{
  z-index: 1;
  color:#111;
  margin-left: 300px;
  width: 75%;
  height: auto;
  border: 1px white;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  background-color: rgb(250, 250, 250);
  position: relative;
}
table {
        margin-left: auto;
    }
#side{
    margin-left:-70%;
}
#title{
    margin-left:5px;
}
#out{
    align-items: end;
}
table {
            border-collapse: collapse;
            width: 60%;
            margin: 20px auto;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        td[contenteditable="true"] {
            background-color: #f9f9f9;
            cursor: text;
        }
        button{
            padding: 8px 15px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            color: #fff;
            background: #3b2a22;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        button:hover{
            background: #e0c49f;
            color: #3b2a22;
        }
        button{
            padding: 8px 15px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            color: #fff;
            background: #3b2a22;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

    </style>
</head>

    <body>

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
            <a href="orderRecords.php" id="complete">Order Records</a>
            <a href="" id="expenses.php">Expense Report</a>
            <a id="showNavbar">Product Management</a>
            <br><br><br><br><br>
            <a href="logout.php" id="out">Logout</a>
</div>
<div id="productNavbar">
    <a href="staff.php">Edit Highlights</a>
    <a href="MenuStaff.php">Edit Menu</a>
</div>

<button id="clearTable">Clear Table</button>
<button id="printButton">Print Report</button>
<button id="addRow">Add Row</button>
<!--Printable Area-->
<div class="card">

    <p id="title">Expenses Report</p>
    <p id="side">Date: <span id="currentDate"></span></p>

<table id="myTable">
    <thead>
        <tr>
            <th>Product</th>
            <th>Unit</th>
            <th>Cost</th>
            <th>Total</th>
        </tr>
    </thead>

    <tbody>
        
  
    </tbody>
</table>
<br>
</div>
<!---------------->
 

<script src="JS\admin.js"></script>
    </body>
</html>
