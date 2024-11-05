<?php
    session_start();
    require_once "amfcConx.php";
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['paymethod']) && $user_id) {
        $pay_method = $_POST['paymethod'];

        // Loop through each order to check if payment already exists
        $sqlOrdProd = "SELECT DISTINCT op.ORDER_ID, op.ORDER_TOTALAMOUNT, op.GROUP_ID
                    FROM orderprod op
                    LEFT JOIN payment p ON op.ORDER_ID = p.ORDER_ID
                    WHERE op.USER_ID = :user_id AND p.PAY_METHOD IS NULL";
        $stmtOrdProd = $pdo->prepare($sqlOrdProd);
        $stmtOrdProd->bindParam(':user_id', $user_id);
        $stmtOrdProd->execute();

        while ($rowOrdProd = $stmtOrdProd->fetch(PDO::FETCH_ASSOC)) {
            $order_id = $rowOrdProd['ORDER_ID'];
            $total_amount = $rowOrdProd['ORDER_TOTALAMOUNT'];
            $group_id = $rowOrdProd['GROUP_ID'];

            // Calculate total amount for all orders with the same group ID
            $sqlTotalAmount = "SELECT SUM(ORDER_TOTALAMOUNT) AS total_amount 
                            FROM orderprod 
                            WHERE GROUP_ID = :group_id";
            $stmtTotalAmount = $pdo->prepare($sqlTotalAmount);
            $stmtTotalAmount->bindParam(':group_id', $group_id);
            $stmtTotalAmount->execute();
            $total_amount_group = $stmtTotalAmount->fetchColumn();

            // Add 30 pesos to the total amount group
            $total_amount_group += 30;

            // Check if payment already exists for this order
            $sqlCheckPayment = "SELECT * FROM payment WHERE ORDER_ID = :order_id";
            $stmtCheckPayment = $pdo->prepare($sqlCheckPayment);
            $stmtCheckPayment->bindParam(':order_id', $order_id);
            $stmtCheckPayment->execute();

            // Insert payment record if it doesn't exist
            $sqlPayment = "INSERT INTO payment (ORDER_ID, GROUP_ID, PAY_AMOUNT, PAY_METHOD, PAY_STATUS, PAY_DATETIME) 
                        VALUES (:order_id, :group_id, :total_amount_group, :pay_method, 2, CURRENT_TIMESTAMP)";
            $stmtPayment = $pdo->prepare($sqlPayment);
            $stmtPayment->bindParam(':order_id', $order_id);
            $stmtPayment->bindParam(':group_id', $group_id);
            $stmtPayment->bindParam(':total_amount_group', $total_amount_group);
            $stmtPayment->bindParam(':pay_method', $pay_method);
            $stmtPayment->execute();
        }

        $sqlUser = "SELECT * FROM user WHERE USER_ID = :user_id";
        $stmtUser = $pdo->prepare($sqlUser);
        $stmtUser->bindParam(':user_id', $user_id);
        $stmtUser->execute();
        $rowUser = $stmtUser->fetch(PDO::FETCH_ASSOC);

        $sqlOrderDetails = "SELECT op.*, p.PROD_NAME, p.PROD_PRICE 
                            FROM orderprod op 
                            JOIN product p ON op.PROD_ID = p.PROD_ID 
                            WHERE op.USER_ID = :user_id";
        $stmtOrderDetails = $pdo->prepare($sqlOrderDetails);
        $stmtOrderDetails->bindParam(':user_id', $user_id);
        $stmtOrderDetails->execute();

        // Calculate total amount
        $totalAmount = 0;
        $orderDetails = $stmtOrderDetails->fetchAll(PDO::FETCH_ASSOC);

        foreach ($orderDetails as $rowOrderDetail) {
            if ($rowOrderDetail['ORDER_STATUS'] == 2) {
                $totalAmount += $rowOrderDetail['ORDER_TOTALAMOUNT'];
            }
        }

        // Determine status
        $sqlPendingPayments = "SELECT ORDER_STATUS FROM orderprod WHERE USER_ID = :user_id AND ORDER_STATUS = 2";
        $stmtPendingPayments = $pdo->prepare($sqlPendingPayments);
        $stmtPendingPayments->bindParam(':user_id', $user_id);
        $stmtPendingPayments->execute();
        $pending = $stmtPendingPayments->rowCount() > 0;
    }
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
        margin-bottom: 20px; /* Add margin to the bottom */
        line-height: 1.5; /* Increase line height for better readability */
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
    /* Modal styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        text-align: center;
        border: 1px solid #888;
        width: 900px; /* Adjust the width as needed */
        height:750px;
        max-width: 1000px;
        max-height: 1000px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
    }
</style>
</head>
<body>
    <div class="container">
        <h1>Order Receipt</h1>
        <div class="order-details">
            <h2>Order Summary</h2>
            <p><strong>User ID:</strong> <?php echo $user_id ? $user_id : 'N/A'; ?></p>
            <p><strong>Name:</strong> <?php echo isset($rowUser['USER_FNAME']) && isset($rowUser['USER_LNAME']) ? $rowUser['USER_FNAME'] . " " . $rowUser['USER_LNAME'] : 'N/A'; ?></p>
            <p><strong>Address:</strong> <?php echo $rowUser['USER_ADDRESS']?></p>
            <p><strong>Phone:</strong> <?php echo $rowUser['USER_PHONE']?></p>
            <p><strong>Date Time:</strong> <?php echo isset($rowOrderDetail['ORDER_DATETIME']) ? $rowOrderDetail['ORDER_DATETIME'] : 'N/A'; ?></p>
            <h3>Items:</h3>
            <?php if ($stmtOrderDetails->rowCount() > 0) : ?>
                <table>
                    <tr><th>Product</th><th>Quantity</th><th>Price</th><th>Total</th></tr>
                    <?php foreach ($orderDetails as $rowOrderDetail) : ?>
                        <?php if ($rowOrderDetail['ORDER_STATUS'] == 2) : ?>
                            <tr>
                                <td><?php echo $rowOrderDetail['PROD_NAME']; ?></td>
                                <td><?php echo $rowOrderDetail['ORDER_QUANTITY']; ?></td>
                                <td>₱<?php echo number_format($rowOrderDetail['PROD_PRICE'], 2); ?></td>
                                <td>₱<?php echo number_format($rowOrderDetail['ORDER_TOTALAMOUNT'], 2); ?></td>
                            </tr>
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
    <!-- Modal for completed transaction -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>Transaction Completed!</p>
            <button class="button" onclick="returnToHomepage()">Return to Homepage</button>
        </div>
    </div>
    <script>
       // Get the modal
        var modal = document.getElementById("myModal");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // Function to return to homepage
        function returnToHomepage() {
            window.location.href = "userPage.php";
        }

        // Close the modal when the user clicks on <span> (x)
        span.onclick = function(event) {
            event.stopPropagation(); // Prevent modal from closing when the span is clicked
            returnToHomepage(); // Redirect to homepage
        };

        // Check if the status is completed and show modal
        var status = document.querySelector('.status').textContent.trim();
        if (status === 'COMPLETED') {
            modal.style.display = "block";
        }
    </script>
</body>
</html>
