<?php
require "amfcConx.php";
session_start();

$email = $_POST['email'];
$password = $_POST['pass'];

try {
    // Prepare the SQL query using placeholders
    $sqlVerify = "SELECT * FROM user WHERE USER_EMAIL = :email";

    // Prepare the statement
    $stmt = $pdo->prepare($sqlVerify);

    // Bind parameters
    $stmt->bindParam(':email', $email);

    // Execute the statement
    $stmt->execute();

    // Fetch the result
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if a row was returned
    if ($row) {
        $hashed_pass = $row['USER_PASS']; 

        if(password_verify($password, $hashed_pass)){
            $_SESSION['user_id'] = $row['USER_ID'];
            $_SESSION['user_level'] = $row['USER_LEVEL'];

            if ($row['USER_LEVEL'] == 1) {
                header("Location: adminPage.php");
                exit();
            } else if ($row['USER_LEVEL'] == 2) {
                header("Location: userPage.php");
                exit();
            }
        }else {
            header("Location: logPageError.php");
            exit();
        }
    } else {
        header("Location: logPageError.php");
        exit();
    }
} catch (PDOException $e) {
    // Handle any exceptions
    echo "Error: " . $e->getMessage();
    // You may choose to handle errors differently, like logging them or showing a user-friendly message
}

// Close the database connection
$pdo = null;
?>
