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
    <title>Product Table</title>
    <style>
        body {
            background-color: #5c6961;
            margin: 20px;
        }
        table {
            background-color: white;
            border-collapse: collapse;
            width: 100%;
        }
        h1 {
            color: white;
            text-shadow:
                -1px -1px 0 #000,  
                1px -1px 0 #000,
                -1px 1px 0 #000,
                1px 1px 0 #000;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .edit-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 6px 12px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
        }
        .edit-button:hover {
            background-color: #0056b3;
        }
        .back-button {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<h1>Products</h1>
<table border="1">
    <thead>
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Product Price</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <!-- PHP code to fetch data from database and display in table -->
        <?php
        try {
            // Select data from product table
            $sql = "SELECT * FROM product";
            $stmt = $pdo->query($sql);
            
            // Display data in table
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $prod_id = $row['PROD_ID'];
                
                echo "<tr>";
                echo "<td>" . $row['PROD_ID'] . "</td>";
                echo "<td>" . $row['PROD_NAME'] . "</td>";
                echo "<td>" . $row['PROD_PRICE'] . "</td>";
                echo "<td>";
                echo "<form method='POST' action='adminProdEdit.php'>";
                echo "<input type='hidden' name='prodid' value='$prod_id'>";
                echo "<input type='submit' name='edit-button' value='EDIT'>";
                echo "</form>";
                echo "<form method='POST' action='adminProdDelete.php' onsubmit='return confirm(\"Are you sure you want to delete this product?\")'>";
                echo "<input type='hidden' name='prodid' value='$prod_id'>";
                echo "<input type='submit' name='delete-button' value='DELETE'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        ?>
    </tbody>
</table>
<a href="adminProd.php" class="back-button">Back</a>
</body>
</html>
