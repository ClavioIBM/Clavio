<?php 
session_start();
require "amfcConx.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: logPage.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$group_id = $_SESSION['group_id'];
$order_id = $_SESSION['order_id'];


try {
    // Fetch the user's phone number
    $sqlFetchPhoneNumber = "SELECT USER_PHONE FROM user WHERE USER_ID = :userID";
    $stmt = $pdo->prepare($sqlFetchPhoneNumber);
    $stmt->bindParam(':userID', $user_id);
    $stmt->execute();
    $userResult = $stmt->fetch(PDO::FETCH_ASSOC);

    $userPhone = $userResult ? $userResult['USER_PHONE'] : "Phone not found";

    // Fetch the sum of ORDER_TOTALAMOUNT for the user
    $sqlFetchTotalAmount = "SELECT SUM(ORDER_TOTALAMOUNT) AS totalAmount FROM orderprod WHERE USER_ID = :userID AND GROUP_ID = :groupID AND ORDER_STATUS = '2'";
    $stmt = $pdo->prepare($sqlFetchTotalAmount);
    $stmt->bindParam(':userID', $user_id);
    $stmt->bindParam(':groupID', $group_id);
    $stmt->execute();
    $totalAmountResult = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalAmount = $totalAmountResult ? $totalAmountResult['totalAmount'] : 0;
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GCash Payment</title>
    <link rel="stylesheet" href="userPayGcash.css">
    <script src="https://kit.fontawesome.com/1fd0899690.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <div id="paymentlogo">
            <img src="images/GCashLogo.png">
            <h2>GCash Payment</h2>
            <?php $totalAmount += 30;?>
        </div>

        <a href="userPayment.php"><i class="fa-solid fa-chevron-left"></i>  Back to Payment Methods</a>
        <form action="userPayGcashSubmit.php" method="post">
            <div class="payment-fields">
                <label for="payment_amount">Payment Amount:</label><br>
                <input type="number" id="payment_amount" name="payment_amount" value="<?php echo $totalAmount; ?>" readonly><br>
                <span>Please note that payment fee is non-refundable.</span>
            </div>
            <label for="gcash_number">GCash Number:</label><br>
            <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
            <input type="hidden" name="order_id" value="<?php echo $order_id ?>">
            <input type="text" id="gcash_number" name="gcash_number" value="<?php echo $userPhone; ?>" required><br>
            <button type="submit">Pay via GCash</button>
        </form>
    </div>
</body>
</html>
