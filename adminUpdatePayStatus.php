<?php 
    session_start();
    require "amfcConx.php";

    $user_id = $_SESSION['user_id'];

    $order_id = $_POST['order_id'];
    $pay_status = $_POST['pay_status'];

    // Update payment status for the selected order
    $sql_update = "UPDATE payment SET PAY_STATUS = :pay_status WHERE ORDER_ID = :order_id";

    // Prepare the SQL statement
    $stmt_update = $pdo->prepare($sql_update);

    // Bind parameters
    $stmt_update->bindParam(':pay_status', $pay_status, PDO::PARAM_INT);
    $stmt_update->bindParam(':order_id', $order_id, PDO::PARAM_INT);

    // Execute the statement
    if ($stmt_update->execute()) {
        // Fetch group ID of the selected order 
        $sql_group_id = "SELECT GROUP_ID FROM orderprod WHERE ORDER_ID = :order_id";
        $stmt_group_id = $pdo->prepare($sql_group_id);
        $stmt_group_id->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt_group_id->execute();
        $row_group_id = $stmt_group_id->fetch(PDO::FETCH_ASSOC);
        $group_id = $row_group_id['GROUP_ID'];

        // Update payment status for all orders in the same group with the same user ID
        $sql_update_orderprod = "UPDATE orderprod 
                                SET ORDER_STATUS = :pay_status 
                                WHERE GROUP_ID = :group_id";
        $stmt_update_orderprod = $pdo->prepare($sql_update_orderprod);
        $stmt_update_orderprod->bindParam(':pay_status', $pay_status, PDO::PARAM_INT);
        $stmt_update_orderprod->bindParam(':group_id', $group_id, PDO::PARAM_INT);
        $stmt_update_orderprod->execute();

        // Update payment status for all payments in the same group
        $sql_update_payment = "UPDATE payment 
                            SET PAY_STATUS = :pay_status 
                            WHERE ORDER_ID IN (SELECT ORDER_ID FROM orderprod WHERE GROUP_ID = :group_id)";
        $stmt_update_payment = $pdo->prepare($sql_update_payment);
        $stmt_update_payment->bindParam(':pay_status', $pay_status, PDO::PARAM_INT);
        $stmt_update_payment->bindParam(':group_id', $group_id, PDO::PARAM_INT);
        $stmt_update_payment->execute();

        header("Location: adminTransactions.php");
    }
?>
