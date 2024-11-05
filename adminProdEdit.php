<?php
    session_start();
    require "amfcConx.php";

    if (!isset($_SESSION['user_id'])) {
        header("Location: logPage.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $prodid = $_POST['prodid'];

    try {

        $stmt = $pdo->prepare("SELECT * FROM product WHERE PROD_ID = :prodid");
        $stmt->bindParam(':prodid', $prodid);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product Information</title>
    <style>
        body {
            background-color: #5c6961;
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        .container {
            background-color: white;
            max-width: 450px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        form {
            max-width: 100%;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"] {
            width: calc(100% - 16px);
            padding: 6px;
            margin-bottom: 10px;
        }
        button[type="submit"] {
            padding: 8px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            display: block;
            margin-top: 10px;
            margin-left: auto;
            margin-right: auto;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
        .back-button {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            text-align: center;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            line-height: 1; /* Center the text vertically */
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Edit Product Information</h1>
    <form method="POST" action="adminProdEditUp.php">
        <label for="prodid">Product ID:</label>
        <input type="text" id="prodid" name="prodid" value = "<?php echo $row['PROD_ID']?>" readonly>

        <label for="prodname">Product Name:</label>
        <input type="text" id="prodname" name="prodname" value = "<?php echo $row['PROD_NAME']?>"  required>

        <label for="prodprice">Product Price:</label>
        <input type="text" id="prodprice" name="prodprice" value = "<?php echo $row['PROD_PRICE']?>" required>
        
        <input type="submit" name="submit" value ="Update">
    </form>
    <a href="adminViewProducts.php" class="back-button">Back</a>
    </div>
</body>
</html>