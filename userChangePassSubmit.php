<?php
session_start();
require "amfcConx.php"; // Assuming you have a PDO connection file

if (!isset($_SESSION['user_id'])) {
    header("Location: logPage.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$oldPass = $_POST['oldpass'];
$newPass = $_POST['newpass'];
$confirmPass = $_POST['confirmpass'];

try {
    // Fetch the current hashed password from the database
    $stmt = $pdo->prepare("SELECT USER_PASS FROM user WHERE USER_ID = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        $hashedOldPass = $row['USER_PASS'];
        
        // Verify the old password
        if (password_verify($oldPass, $hashedOldPass)) {
            // Check if the new password and confirm password match
            if ($newPass === $confirmPass) {
                // Hash the new password
                $hashedNewPass = password_hash($newPass, PASSWORD_DEFAULT);
                
                // Update the password in the database
                $stmt = $pdo->prepare("UPDATE user SET USER_PASS = ? WHERE USER_ID = ?");
                $stmt->execute([$hashedNewPass, $user_id]);
                
                // Redirect to userPage.php after successful password update
                header("Location: userPage.php");
                exit();
            } else {
                echo "<script>alert('Error: New password and confirm password do not match.'); window.location='userChangePass.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Error: Old password is incorrect.'); window.location='userChangePass.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Error: User not found.'); window.location='userChangePass.php';</script>";
            exit();
    }
} catch (Exception $e) {
    header("Location: userChangePass.php");
    exit();
}
?>
