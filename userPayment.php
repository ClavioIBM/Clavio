<?php
    session_start();
    require_once "amfcConx.php";
    
    if (!isset($_SESSION['user_id'])) {
        header("Location: amfcConx.php");
        exit();
    }
    
    $user_id = $_SESSION['user_id'];
    $group_id = $_SESSION['group_id'];
    $order_id = $_SESSION['order_id'];
   
    
    // Prepare and execute SQL query using PDO
    $sqlOrdProd = "SELECT op.*, p.* FROM orderprod op JOIN product p ON op.PROD_ID = p.PROD_ID WHERE op.USER_ID = :user_id AND op.ORDER_STATUS = 2";
    $stmtOrdProd = $pdo->prepare($sqlOrdProd);
    $stmtOrdProd->bindParam(':user_id', $user_id);
    $stmtOrdProd->execute();
    
    // Fetch the first row of the result set
    $rowOrdProd = $stmtOrdProd->fetch(PDO::FETCH_ASSOC);
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fast Food Kiosk Payment Options</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center; /* Center the content horizontally */
        align-items: center; /* Center the content vertically */
        min-height: 100vh; /* Ensure the body covers the entire viewport height */
    }
    .container {
        max-width: 80%; /* Set maximum width to 80% of the viewport */
        margin: 5% auto; /* Center the container vertically and horizontally */
        padding: 5%;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        align-items: center; 
    }

    h1 {
        text-align: center;
        margin-bottom: 30px;
    }
    .payment-option {
        text-align: center;
        display: flex;
        flex-direction: column; /* Arrange items vertically */
        align-items: center; /* Center items horizontally */
        margin-bottom: 20px;
    }
    .payment-option {
        flex: 1;
        text-align: center;
        font-size: 20px;
        width: 300px;
        padding: 10px;      
        border: 1px solid #ccc;
        border-radius: 5px;
        cursor: pointer;
        margin-bottom: 20px; /* Add space between items */
    }
    .payment-option:hover {
        background-color: #f9f9f9;
    }

    .payment-option[value='CASH ON DELIVERY'] {
    background-color: #4CAF50; /* Green */
    color: white;
    }

    .payment-option[value='CASH ON DELIVERY']:hover {
    background-color: #3c863f; /* Green */
    }

    /* Style for the GCash button */
    .payment-option[value='GCASH'] {
        background-color: #3498db; /* Blue */
        color: white;
    }
    .payment-option[value='GCASH']:hover {
        background-color: #2c76a7; /* Blue */     
    }

    .cancel-button {
        display: block;
        width: 100px;
        padding: 10px;
        text-align: center;
        background-color: #e74c3c;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
        margin-top: 50px;

    }
    .cancel-button:hover {
        background-color: #c0392b;
    }
</style>
</head>
<body>
    <div class="container">
        <h1>Select Payment Option</h1>
        <div class="payment-options">
        <form method="POST" action="userPayStatus.php">
            <input type="hidden" name="paymethod" value="1">
            <input type="submit" class='payment-option' value='CASH ON DELIVERY'/>
        </form>
        <form method="POST" action="userPayGcash.php">
            <input type="hidden" name="order_id"  value="<?php echo $order_id ?>">
            <input type="hidden" name="group_id"  value="<?php echo $group_id ?>">
            <input type="submit" class='payment-option' value='GCASH'/>
        </form>
        </div>
        <center><a href="userCart.php" class="cancel-button">Cancel</a></center>
    </div>
</body>
</html>