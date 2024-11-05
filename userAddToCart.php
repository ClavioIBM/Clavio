<?php
session_start();
require "amfcConx.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: logPage.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the form is submitted and the necessary data is provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addToCart']) && isset($_POST['product']) && isset($_POST['quantity'])) {
    $product = $_POST['product'];
    $quantity = $_POST['quantity'];

    // Fetch product details
    $sqlProd = "SELECT * FROM product WHERE PROD_NAME = :product";
    $stmtProd = $pdo->prepare($sqlProd);
    $stmtProd->bindParam(':product', $product);
    $stmtProd->execute();
    $rowProd = $stmtProd->fetch(PDO::FETCH_ASSOC);
    $prodId = $rowProd['PROD_ID'];
    $prodPrice = $rowProd['PROD_PRICE'];

    // Calculate total amount
    $totalAmount = ($quantity * $prodPrice);

    // Check the latest order status of the user
    $latestOrderStatus = getLatestOrderStatus($user_id, $pdo);

    // Start a transaction
    $pdo->beginTransaction();

    // If the latest order status is 1 or no previous orders found, generate a new group ID
    if ($latestOrderStatus == 1 || $latestOrderStatus === null) {
        $groupId = generateNewGroupId($pdo);
    } else {
        // Use the existing group ID from the latest order
        $groupId = getLatestGroupId($user_id, $pdo);
    }

    // Insert data into orderprod table
    $sqlInsert = "INSERT INTO orderprod (PROD_ID, GROUP_ID, USER_ID, ORDER_STATUS, ORDER_QUANTITY, ORDER_TOTALAMOUNT)
                  VALUES (:prod_id, :group_id, :user_id, '2', :quantity, :total_amount)";
    $stmtInsert = $pdo->prepare($sqlInsert);
    $stmtInsert->bindParam(':prod_id', $prodId);
    $stmtInsert->bindParam(':group_id', $groupId);
    $stmtInsert->bindParam(':user_id', $user_id);
    $stmtInsert->bindParam(':quantity', $quantity);
    $stmtInsert->bindParam(':total_amount', $totalAmount);
    $stmtInsert->execute();

    // Commit the transaction
    $pdo->commit();

    header("Location: userOrder.php");
    exit();
} else {
    // Redirect to userOrder.php if the required data is not provided
    header("Location: userOrder.php");
    exit();
}

// Function to get the latest order status of a user
function getLatestOrderStatus($userId, $pdo) {
    $sqlLatestOrder = "SELECT ORDER_STATUS 
                       FROM orderprod 
                       WHERE USER_ID = :user_id 
                       ORDER BY ORDER_DATETIME DESC 
                       LIMIT 1";
    $stmtLatestOrder = $pdo->prepare($sqlLatestOrder);
    $stmtLatestOrder->bindParam(':user_id', $userId);
    $stmtLatestOrder->execute();
    $rowLatestOrder = $stmtLatestOrder->fetch(PDO::FETCH_ASSOC);

    return $rowLatestOrder ? $rowLatestOrder['ORDER_STATUS'] : null;
}

// Function to generate a new group ID
function generateNewGroupId($pdo) {
    $sqlGroupId = "SELECT MAX(GROUP_ID) AS max_group_id FROM orderprod";
    $stmtGroupId = $pdo->query($sqlGroupId);
    $rowGroupId = $stmtGroupId->fetch(PDO::FETCH_ASSOC);
    return $rowGroupId ? ($rowGroupId['max_group_id'] + 1) : 1;
}

// Function to get the latest group ID of a user
function getLatestGroupId($userId, $pdo) {
    $sqlLatestGroup = "SELECT GROUP_ID 
                       FROM orderprod 
                       WHERE USER_ID = :user_id 
                       ORDER BY ORDER_DATETIME DESC 
                       LIMIT 1";
    $stmtLatestGroup = $pdo->prepare($sqlLatestGroup);
    $stmtLatestGroup->bindParam(':user_id', $userId);
    $stmtLatestGroup->execute();
    $rowLatestGroup = $stmtLatestGroup->fetch(PDO::FETCH_ASSOC);

    return $rowLatestGroup ? $rowLatestGroup['GROUP_ID'] : null;
}
?>
