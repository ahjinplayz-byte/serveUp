<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Checkout</title>
    <style>
        body{
        text-align: center;
        z-index: -1000;
        background: #3b2a22;
    }
        form{
         border-radius: 5px;
         text-align: center;
         width: 300px;
         margin-left: 600px;
         margin-top: 200px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
         background: linear-gradient(to right, #cda37c, #f5e7d0);
         font-family: "Ubuntu", sans-serif;
         font-weight: 700;
}
        p{
         font-size: 40px;
         font-weight: bold; 
         padding-top:10px;
}
        button{
            padding: 8px 15px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            color: #fff;
            background: #3b2a22;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            margin-bottom: 10px;
        }
        input[type="text"]{
            padding: 8px 15px;
            border-radius: 5px;
            margin-right: 10px;
            border: 1px solid #ccc;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        input[type="password"]{
            padding: 8px 15px;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        radio {
        display: inline-flex;
        align-items: center;
        }
        radio-input {
        width: 20px; 
        height: 20px; 
        margin-right: 10px;
        }
        </style>
</head>

<body>

<form method="POST" action="insert.php">
    <P>Checkout</P>
    <input type="hidden" name="cart" id="cart-data">

    <label>Name:</label>
    <input type="text" name="name" required>

    <br><br>

    <label>Order Type:</label>

    <label>
        <input type="radio" name="orderType" value="Pickup" required>
        Pickup
    </label>

    <label>
        <input type="radio" name="orderType" value="Delivery">
        Delivery
    </label>

    <br><br>

    <label style=" margin-right: 30px;">Time:</label>

    <label>
        <input type="radio" name="time" value="9:15 AM" required>
        9:15 AM
    </label>

    <label>
        <input type="radio" name="time" value="11:15 AM">
        11:15 AM
    </label>

    <br><br>

    <label style=" margin-right: 30px;">Payment:</label>

    <label>
        <input type="radio" name="payment" value="Cash" required>
        Cash
    </label>

    <label>
        <input type="radio" name="payment" value="Gcash">
        GCash
    </label>

    <br><br>

    <button type="submit">
        Place Order
    </button>

</form>
<script>
const cart = localStorage.getItem("serveup_cart");

document.getElementById("cart-data").value = cart || "[]";
</script>
</body>
</html>