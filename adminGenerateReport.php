<?php
    session_start();
    require "amfcConx.php";

    if (!isset($_SESSION['user_id'])) {
        header("Location: logPage.php");
        exit();
    }
  
    $user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center; /* Horizontally center the container */
            align-items: center; /* Vertically center the container */
            height: 100vh; /* Ensure the container covers the entire viewport */
        }
        .container {
            border: 2px solid #007bff;
            border-radius: 10px;
            padding: 30px;
            background-color: #fff;
            max-width: 600px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 50px;
            color: #333;
        }
        .button-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }
        button:hover {
            background-color: #0056b3;
        }
        .back-button-container {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .back-button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background-color: #e93737;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .back-button:hover {
            background-color: #c53131;
        }
        .container {
            background-color: white;
            max-width: 600px;
            margin: 0 auto; /* Center the container */
            padding: 20px; /* Add padding inside the container */
            border: 2px solid #ddd; /* Add a border */
            border-radius: 5px; /* Add border radius for rounded corners */
            text-align: center; /* Center text */
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Generate Table Report</h1>
    <div class="button-container">
        <form method="POST" action="adminProductReport.php">
            <button type="submit">Product</button>
        </form>

        <form method="POST" action="adminOrderReport.php">
            <button type="submit">Orders</button>
        </form>
        
        <form method="POST" action="adminUserList.php">
            <button type="submit">User</button>
        </form>
    </div>
    <form action="adminPage.php" method="POST">
        <button type="submit" class="back-button">Back</button>
    </form>
    </div>
</body>
</html>