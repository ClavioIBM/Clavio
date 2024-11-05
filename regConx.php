<?php
require "amfcConx.php";

// Initialize variables to store user input
$cFirst = $_POST["fname"];
$cLast = $_POST["lname"];
$cEmail = $_POST["email"];
$cAddress = $_POST["address"];
$cPhoneNum = $_POST["phone"];
$cPswd = $_POST["pass"];

// Store original input for fields with errors
$originalFirst = $cFirst;
$originalLast = $cLast;
$originalAddress = $cAddress;

try {
    // Check for duplicate email
    $sql_check_email = "SELECT * FROM user WHERE USER_EMAIL = :email";
    $stmt_email = $pdo->prepare($sql_check_email);
    $stmt_email->bindParam(':email', $cEmail);
    $stmt_email->execute();

    if ($stmt_email->rowCount() > 0) {
        // Duplicate email found
        echo "<script>alert('Error: The email address is already registered.'); window.location='regPage.php';</script>";
        exit();
    } else {
        // Check for duplicate phone number
        $sql_check_phone = "SELECT * FROM user WHERE USER_PHONE = :phone";
        $stmt_phone = $pdo->prepare($sql_check_phone);
        $stmt_phone->bindParam(':phone', $cPhoneNum);
        $stmt_phone->execute();

        if ($stmt_phone->rowCount() > 0) {
            // Duplicate phone number found
            echo "<script>alert('Error: The phone number is already registered.'); window.location='regPage.php';</script>";
            exit();
        } else {
            // No duplicates, proceed with the insert
            $hashedPassword = password_hash($cPswd, PASSWORD_DEFAULT);
            $sql_insert = "INSERT INTO user (USER_ID, USER_FNAME, USER_LNAME, USER_EMAIL, USER_ADDRESS, USER_PHONE, USER_PASS, USER_LEVEL, USER_DATETIME) 
                        VALUES (NULL, :fname, :lname, :email, :address, :phone, :password, '2', current_timestamp())";
            $stmt_insert = $pdo->prepare($sql_insert);
            $stmt_insert->bindParam(':fname', $cFirst);
            $stmt_insert->bindParam(':lname', $cLast);
            $stmt_insert->bindParam(':email', $cEmail);
            $stmt_insert->bindParam(':address', $cAddress);
            $stmt_insert->bindParam(':phone', $cPhoneNum);
            $stmt_insert->bindParam(':password', $hashedPassword);
            
            if ($stmt_insert->execute()) {
                echo "<script>alert('Account Created Successfully!');  window.location='logPage.php';</script>";
                exit();
            } else {
                echo "<script>alert('Error: Unable to register. Please try again.');</script>";
                exit();
            }
        }
    }
} catch (PDOException $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

$pdo = null; // Close the connection
?>
