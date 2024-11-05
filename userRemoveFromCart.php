<?php
    session_start();
    require_once "amfcConx.php";

    if (!isset($_SESSION['user_id'])) {
        header("Location: logPage.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $prod_id = $_POST['prod_id'];
    $ord_status = $_POST['ord_status'];

    try {
        // Check if there are payments associated with the order
        $sqlCheckPayments = "SELECT * FROM payment WHERE ORDER_ID IN (SELECT ORDER_ID FROM orderprod WHERE PROD_ID = :prod_id AND USER_ID = :user_id AND ORDER_STATUS = :ord_status)";
        $stmtCheckPayments = $pdo->prepare($sqlCheckPayments);
        $stmtCheckPayments->bindParam(':prod_id', $prod_id);
        $stmtCheckPayments->bindParam(':user_id', $user_id);
        $stmtCheckPayments->bindParam(':ord_status', $ord_status);
        $stmtCheckPayments->execute();

        if ($stmtCheckPayments->rowCount() > 0) {
            // If payments exist, delete them first
            $sqlDeletePayments = "DELETE FROM payment WHERE ORDER_ID IN (SELECT ORDER_ID FROM orderprod WHERE PROD_ID = :prod_id AND USER_ID = :user_id AND ORDER_STATUS = :ord_status)";
            $stmtDeletePayments = $pdo->prepare($sqlDeletePayments);
            $stmtDeletePayments->bindParam(':prod_id', $prod_id);
            $stmtDeletePayments->bindParam(':user_id', $user_id);
            $stmtDeletePayments->bindParam(':ord_status', $ord_status);
            $stmtDeletePayments->execute();

            // After deleting payments, delete the order from orderprod
            $sqlRemove = "DELETE FROM orderprod WHERE PROD_ID = :prod_id AND USER_ID = :user_id AND ORDER_STATUS = :ord_status";
            $stmtRemove = $pdo->prepare($sqlRemove);
            $stmtRemove->bindParam(':prod_id', $prod_id);
            $stmtRemove->bindParam(':user_id', $user_id);
            $stmtRemove->bindParam(':ord_status', $ord_status);
            $stmtRemove->execute();

            header("Location: userCart.php");
            exit();
        } else {
            // If no payments exist, directly delete the order from orderprod
            $sqlRemove = "DELETE FROM orderprod WHERE PROD_ID = :prod_id AND USER_ID = :user_id AND ORDER_STATUS = :ord_status";
            $stmtRemove = $pdo->prepare($sqlRemove);
            $stmtRemove->bindParam(':prod_id', $prod_id);
            $stmtRemove->bindParam(':user_id', $user_id);
            $stmtRemove->bindParam(':ord_status', $ord_status);
            $stmtRemove->execute();

            header("Location: userCart.php");
            exit();
        }
    } catch (PDOException $e) {
        // Handle PDO exceptions
        echo "Error: " . $e->getMessage();
    }
?>
