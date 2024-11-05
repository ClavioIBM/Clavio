<?php
session_start();
require "amfcConx.php";

$prodid = $_POST['prodid'];

try {
    $stmt = $pdo->prepare("DELETE FROM product WHERE PROD_ID = :prodid");
    $stmt->bindParam(':prodid', $prodid);
    $stmt->execute();

    header("Location: adminViewProducts.php");
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
