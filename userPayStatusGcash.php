<?php
session_start();
require_once "amfcConx.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: logPage.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$group_id = $_SESSION['group_id'];
$order_id = $_SESSION['order_id'];

// Fetch user details
$sqlUser = "SELECT * FROM user WHERE USER_ID = :user_id";
$stmtUser = $pdo->prepare($sqlUser);
$stmtUser->bindParam(':user_id', $user_id);
$stmtUser->execute();
$rowUser = $stmtUser->fetch(PDO::FETCH_ASSOC);

// Fetch order details
$sqlOrderDetails = "SELECT op.*, p.PROD_NAME, p.PROD_PRICE FROM orderprod op 
                    JOIN product p ON op.PROD_ID = p.PROD_ID 
                    WHERE op.USER_ID = :user_id AND op.GROUP_ID = $group_id AND op.ORDER_STATUS = 1";
$stmtOrderDetails = $pdo->prepare($sqlOrderDetails);
$stmtOrderDetails->bindParam(':user_id', $user_id);
$stmtOrderDetails->execute();
$orderDetails = $stmtOrderDetails->fetchAll(PDO::FETCH_ASSOC);

$totalAmount = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Receipt</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }
    .container {
        max-width: 600px;
        margin: 0 auto;
        margin-bottom: 50px;
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    h1, h2, p {
        align-items: flex-end;
    }
    h1 {
        font-size: 25px;
    }
    h2 {
        font-size: 20px;
    }
    .order-details {
        margin-top: 20px;
        margin-bottom: 20px;
        line-height: 1.5;
    }
    .status {
        font-weight: bold;
        color: green;
        text-align: left;
    }
    .button-container {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
    .button {
        padding: 10px 20px;
        margin: 0 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        background-color: #007bff;
        color: #fff;
        text-decoration: none;
    }
    .button:hover {
        background-color: #0056b3;
    }
    input[type="submit" i] {
        padding: 10px 20px;
        margin: 0 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        background-color: #dc3545;
        color: #fff;
        text-decoration: none;
    }
    input[type="submit" i]:hover {
        background-color: #0056b3;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th, td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background-color: #f2f2f2;
    }
</style>
</head>
<body>
    <div class="container">
        <h1>Order Receipt</h1>
        <div class="order-details">
            <h2>Order Summary</h2>
            <p><strong>User ID:</strong> <?php echo $rowUser['USER_ID'] ?? 'N/A'; ?></p>
            <p><strong>Name:</strong> <?php echo isset($rowUser['USER_FNAME']) && isset($rowUser['USER_LNAME']) ? $rowUser['USER_FNAME'] . " " . $rowUser['USER_LNAME'] : 'N/A'; ?></p>
            <p><strong>Address:</strong> <?php echo $rowUser['USER_ADDRESS'] ?? 'N/A'; ?></p>
            <p><strong>Phone:</strong> <?php echo $rowUser['USER_PHONE']?></p>
            <p><strong>Date Time:</strong> <?php echo isset($orderDetails[0]['ORDER_DATETIME']) ? $orderDetails[0]['ORDER_DATETIME'] : 'N/A'; ?></p>
            <h3>Items:</h3>
            <?php if (!empty($orderDetails)) : ?>
                <table>
                    <tr><th>Product</th><th>Quantity</th><th>Price</th><th>Total</th></tr>
                    <?php foreach ($orderDetails as $rowOrderDetail) : ?>
                        <?php if ($rowOrderDetail['ORDER_STATUS'] == 1) : ?>
                            <tr>
                                <td><?php echo $rowOrderDetail['PROD_NAME']; ?></td>
                                <td><?php echo $rowOrderDetail['ORDER_QUANTITY']; ?></td>
                                <td>₱<?php echo number_format($rowOrderDetail['PROD_PRICE'], 2); ?></td>
                                <td>₱<?php echo number_format($rowOrderDetail['ORDER_TOTALAMOUNT'], 2); ?></td>
                            </tr>
                            <?php $totalAmount += $rowOrderDetail['ORDER_TOTALAMOUNT']; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </table>
            <?php else : ?>
                <p>No items found.</p>
            <?php endif; ?>
            <p><strong>Total:</strong> ₱ <?php echo number_format($totalAmount + 30, 2); ?></p>
            <p>*Delivery Fee of ₱30 is included*</p>
        </div>
        <div class="button-container">
            <button class="button" onclick="window.location.href='userPage.php'">Back to Homepage</button>
        </div>
    </div>
</body>
</html>
