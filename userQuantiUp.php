<?php
session_start();
require_once "amfcConx.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: logPage.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $updQuant = $_POST['quantiUp'];
    
    $prod_id = $_POST['prod'];
    $groupId = getCurrentGroupId($user_id, $pdo);
    // Check if the quantity update is for "minus"
    if ($updQuant == "minus") {
        
        // Check if the current quantity is greater than 1 before allowing subtraction
        $sqlCheckQuantity = "SELECT ORDER_QUANTITY FROM orderprod WHERE PROD_ID = :prod_id AND USER_ID = :user_id AND GROUP_ID = :groupId AND ORDER_STATUS = 2";
        $stmtCheckQuantity = $pdo->prepare($sqlCheckQuantity);
        $stmtCheckQuantity->bindParam(':prod_id', $prod_id);
        $stmtCheckQuantity->bindParam(':user_id', $user_id);
        $stmtCheckQuantity->bindParam(':groupId', $groupId);
        $stmtCheckQuantity->execute();
        $rowCheckQuantity = $stmtCheckQuantity->fetch(PDO::FETCH_ASSOC);

        if ($rowCheckQuantity && $rowCheckQuantity['ORDER_QUANTITY'] > 1) {
            // Prepare and execute SQL to update the quantity and total amount for the specific product
            $sqlUpdateOrdProd = "UPDATE orderprod 
                                 SET ORDER_QUANTITY = ORDER_QUANTITY - 1,
                                     ORDER_TOTALAMOUNT = ORDER_QUANTITY * (SELECT PROD_PRICE FROM product WHERE PROD_ID = :prod_id AND ORDER_STATUS = 2)
                                 WHERE PROD_ID = :prod_id AND USER_ID = :user_id";
            $stmtUpdateOrdProd = $pdo->prepare($sqlUpdateOrdProd);
            $stmtUpdateOrdProd->bindParam(':prod_id', $prod_id);
            $stmtUpdateOrdProd->bindParam(':user_id', $user_id);
            $stmtUpdateOrdProd->execute();

            header("Location: userCart.php");
            exit();
        }
    } elseif ($updQuant == "add") {
        // Check if the current quantity is already zero
        $sqlCheckQuantity = "SELECT ORDER_QUANTITY FROM orderprod WHERE PROD_ID = :prod_id AND USER_ID = :user_id AND GROUP_ID = :groupId";
        $stmtCheckQuantity = $pdo->prepare($sqlCheckQuantity);
        $stmtCheckQuantity->bindParam(':prod_id', $prod_id);
        $stmtCheckQuantity->bindParam(':user_id', $user_id);
        $stmtCheckQuantity->bindParam(':groupId', $groupId);
        $stmtCheckQuantity->execute();
        $rowCheckQuantity = $stmtCheckQuantity->fetch(PDO::FETCH_ASSOC);

        if ($rowCheckQuantity && $rowCheckQuantity['ORDER_QUANTITY'] >= 0) {
            // Prepare and execute SQL to update the quantity and total amount for the specific product
            $sqlUpdateOrdProd = "UPDATE orderprod 
                                 SET ORDER_QUANTITY = ORDER_QUANTITY + 1,
                                     ORDER_TOTALAMOUNT = ORDER_QUANTITY * (SELECT PROD_PRICE FROM product WHERE PROD_ID = :prod_id )
                                 WHERE PROD_ID = :prod_id AND USER_ID = :user_id AND ORDER_STATUS = 2";
            $stmtUpdateOrdProd = $pdo->prepare($sqlUpdateOrdProd);
            $stmtUpdateOrdProd->bindParam(':prod_id', $prod_id);
            $stmtUpdateOrdProd->bindParam(':user_id', $user_id);
            $stmtUpdateOrdProd->execute();

            header("Location: userCart.php");
            exit();
        }
    }
}

function getCurrentGroupId($user_id, $pdo) {
    $currentGroupIdSql = "SELECT GROUP_ID FROM orderprod WHERE USER_ID = :user_id ORDER BY ORDER_DATETIME DESC LIMIT 1";
    $stmt = $pdo->prepare($currentGroupIdSql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($row) ? $row["GROUP_ID"] : null;
}
?>
