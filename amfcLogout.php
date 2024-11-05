<?php
session_start();
require "amfcConx.php"; // Assuming this file contains your database connection

// Retrieve user_id from session (ensure it exists)
if (!isset($_SESSION['user_id'])) {
    header("Location: logPage.php");
    exit();
}
$user_id = $_SESSION['user_id'];

try {
    // Delete orders with ORDER_STATUS = 2 for the current user
    $sqlDeleteOrd = "DELETE FROM orderprod WHERE USER_ID = :user_id AND ORDER_STATUS = '2'";
    $stmt = $pdo->prepare($sqlDeleteOrd);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    // Destroy session
    session_destroy();

    // Redirect to logPage.php
    header("Location: logPage.php");
    exit();
} catch (PDOException $e) {
    // Handle any exceptions
    echo "Error: " . $e->getMessage();
    // You may choose to handle errors differently, like logging them or showing a user-friendly message
}
?>
