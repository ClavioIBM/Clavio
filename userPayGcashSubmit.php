<?php
session_start();
require "amfcConx.php";

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: logPage.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$group_id = $_SESSION['group_id'];

if ($group_id === null) {
    header("Location: userPayment.php");
    exit;
}

// Proceed only if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_amount = isset($_POST['payment_amount']) ? $_POST['payment_amount'] : null;
    if ($payment_amount === null) {
        header("Location: userPayGcash.php");
        exit;
    }

    try {
        // Get all ORDER_IDs for the given GROUP_ID
        $sqlGetOrderIds = "SELECT ORDER_ID FROM orderprod WHERE GROUP_ID = :GROUP_ID";
        $stmt = $pdo->prepare($sqlGetOrderIds);
        $stmt->bindParam(':GROUP_ID', $group_id);
        $stmt->execute();
        $orderIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if ($orderIds) {
            // Insert payment for each order in the group
            $payMethod = 2; // Example pay method for GCash
            foreach ($orderIds as $orderId) {
                $sqlInsertPayment = "INSERT INTO payment (ORDER_ID, GROUP_ID, PAY_AMOUNT, PAY_METHOD, PAY_STATUS, PAY_DATETIME)
                                    VALUES (:ORDER_ID, :GROUP_ID, :PAY_AMOUNT, :PAY_METHOD, 1, CURRENT_TIMESTAMP)";
                $stmt = $pdo->prepare($sqlInsertPayment);
                $stmt->bindParam(':ORDER_ID', $orderId);
                $stmt->bindParam(':GROUP_ID', $group_id);
                $stmt->bindParam(':PAY_AMOUNT', $payment_amount);
                $stmt->bindParam(':PAY_METHOD', $payMethod);
                $stmt->execute();
            }

            // Update booking status to 'Done'
            $sqlUpdateOrder = "UPDATE orderprod SET ORDER_STATUS = '1' WHERE GROUP_ID = :GROUP_ID AND ORDER_STATUS = '2'";
            $stmt = $pdo->prepare($sqlUpdateOrder);
            $stmt->bindParam(':GROUP_ID', $group_id);
            $stmt->execute();
        }

        // Redirect to payment status page
        header("Location: userPayStatusGcash.php");
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Close the database connection
$pdo = null;
?>
