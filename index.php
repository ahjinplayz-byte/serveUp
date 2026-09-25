<?php
session_start();

$error = "";

$correctUsername = "administrator";
$correctPassword = "12345";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $password = $_POST["password"];

    if ($username === $correctUsername && $password === $correctPassword) {

        $_SESSION["loggedIn"] = true;
        $_SESSION["username"] = $username;

        header("Location: admin.php");
        exit();

    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="readStyle.css">
    <style>
        body{
        text-align: center;
        z-index: -1000;
        background: #3b2a22;
    }
        .card{
            font-family: "Ubuntu", sans-serif;
         border-radius: 5px;
         text-align: center;
         width: 300px;
         margin-left: 600px;
         margin-top: 200px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
         background: linear-gradient(to right, #cda37c, #f5e7d0);
}
        p{
         font-size: 40px;
         font-weight: bold; 
         padding: 20px 0;
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
            border: 1px solid #ccc;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        input[type="password"]{
            padding: 8px 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        img{
            width: 20px;
            height: 20px;
            margin-bottom: 20px;
            position: absolute; 
        }
        #username{
            margin-left: -20px;
            margin-TOP: 5px;
        }
        #password{
            margin-left: -20px;
            margin-TOP: 60px;
        }

        </style>
    <title>Login</title>
</head>

<body>
<div class="card">
    <p>Login</p>
<div id="card">
    <form method="POST">

    <img id="username"src="username.png" alt="User Icon">
    <img id="password"src="password.png" alt="Password Icon" >

        <input 
            type="text" 
            name="username" 
            placeholder="Username"
            required
        >

        <br><br>

        <input 
            type="password" 
            name="password" 
            placeholder="Password"
            required
        >

        <br><br>

        <button type="submit">Login</button>
        

    </form>
</div>
    <p><?php echo $error; ?></p>
</div>
</body>
</html>