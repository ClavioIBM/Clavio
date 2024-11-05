<?php
    session_start();
    require "amfcConx.php";
    
    $user_id = $_SESSION['user_id'];
    $prod_id = $_POST['product_id'];
    $avail = $_POST['availability'];

    // Prepare and execute the SQL statement
    try {
        $sqlProd = "UPDATE product SET PROD_AVAIL = :avail WHERE PROD_ID = :prod_id";
        $stmt = $pdo->prepare($sqlProd);
        $stmt->bindParam(':avail', $avail);
        $stmt->bindParam(':prod_id', $prod_id);
        $stmt->execute();
        
        // Redirect to adminProdAvail.php after successful update
        header("Location: adminProdAvail.php");
        exit();
    } catch(PDOException $e) {
        // Handle query errors gracefully
        echo "Update failed: " . $e->getMessage();
        exit();
    }
    
?>