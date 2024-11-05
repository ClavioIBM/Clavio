<?php
session_start();
require "amfcConx.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: logPage.php");
    exit();
}

if(isset($_POST['submit'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_category = $_POST['product_category']; // Added for product category selection

    // File upload handling
    $target_directory = __DIR__ . "/images/";
    $target_file = $target_directory . basename($_FILES["product_image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["product_image"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }

    if (file_exists($target_file)) {
        $uploadOk = 0;
    }

    if ($_FILES["product_image"]["size"] > 50000000000) {
        $uploadOk = 0;
    }

    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
    } else {
        try {
            // Insert the product into the database using prepared statement
            $sqlInsertProduct = "INSERT INTO product (PROD_NAME, PROD_PRICE, PROD_AVAIL, PROD_CATEGORY, PROD_DATETIME) 
                                VALUES (:product_name, :product_price, 1, :product_category, current_timestamp())";
            $stmt = $pdo->prepare($sqlInsertProduct);
            $stmt->bindParam(':product_name', $product_name);
            $stmt->bindParam(':product_price', $product_price);
            $stmt->bindParam(':product_category', $product_category); // Bind category value
            $stmt->execute();

            $product_id = $pdo->lastInsertId();

            $new_file_name = "img" . $product_id . "." . $imageFileType;
            move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_directory . $new_file_name);
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <style>
       body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: white;
            max-width: 600px;
            margin: 20px auto; /* Center the container */
            padding: 20px; /* Add padding inside the container */
            border: 2px solid #ddd; /* Add a border */
            border-radius: 5px; /* Add border radius for rounded corners */
        }
        h1,h3 {
            text-align: center;
        }
        form {
            margin-bottom: 20px;
            max-width: 400px; /* Limiting form width */
            margin-left: auto;
            margin-right: auto;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], select {
            width: calc(100% - 16px); /* Adjusted width */
            padding: 6px; /* Reduced padding */
            margin-bottom: 10px;
        }
        button[type="submit"] {
            padding: 8px 20px; /* Adjusted padding */
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .edit-button {
            padding: 6px 10px; /* Adjusted padding */
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            text-decoration: none; /* Remove default link underline */
        }
        .edit-button:hover {
            background-color: #0056b3;
        }

        .back-button {
            margin: 20px auto; 
            width: 200px;
            display: block;
            text-align: center;
            margin-top: 40px;
            margin-bottom: 0px;
            text-decoration: none;
            background-color: #e93737;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .back-button:hover {
            background-color: #b73329;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Product Management</h1>
    
    <form method="post" enctype="multipart/form-data">
        <h3>Add a Product</h3>
        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" required>
        <br><br>
        <label for="product_price">Product Price:</label>
        <input type="text" id="product_price" name="product_price" required>
        <br><br>
        <label for="product_category">Product Category:</label>
        <select id="product_category" name="product_category" required>
            <option value="Dish">Dish</option>
            <option value="Dessert">Dessert</option>
        </select>
        <br><br>
        <label for="product_image">Product Image:</label>
        <input type="file" id="product_image" name="product_image" accept="image/*" required>
        <br><br>
        <button type="submit" name="submit">Insert Product</button>
    </form>

    <form method="post" action="adminViewProducts.php">
        <button type="submit" name="submit">View Products</button>
    </form>
    <a href="adminPage.php" class="back-button">Back to Homepage</a>
    <br><br>
</div>
</body>
</html>
