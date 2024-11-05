<?php
    session_start();
    require "amfcConx.php";

    $prodid = $_POST['prodid'];
    $prodname = $_POST['prodname'];
    $prodprice = $_POST['prodprice'];

    try {
        $stmt = $pdo->prepare("UPDATE product SET PROD_NAME = :prodname, PROD_PRICE = :prodprice WHERE PROD_ID = :prodid");
        $stmt->bindParam(':prodname', $prodname);
        $stmt->bindParam(':prodprice', $prodprice);
        $stmt->bindParam(':prodid', $prodid);
        $stmt->execute();

        header("Location: adminViewProducts.php");
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
?>
