<?php 
    session_start();
    require "amfcConx.php";

    $user_id = $_SESSION['user_id'];

    $cFirst = $_POST['firstName'];
    $cLast = $_POST['lastName'];
    $cEmail = $_POST['email'];
    $cAddress = $_POST["address"];
    $cPhoneNum = $_POST['phone'];

    // Fetch the current user data from the database
    $stmt = $pdo->prepare("SELECT * FROM user WHERE USER_ID = ?");
    $stmt->execute([$user_id]);
    $currentUserData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if any changes have been made
    if ($currentUserData['USER_FNAME'] != $cFirst ||
        $currentUserData['USER_LNAME'] != $cLast ||
        $currentUserData['USER_EMAIL'] != $cEmail ||
        $currentUserData['USER_ADDRESS'] != $cAddress ||
        $currentUserData['USER_PHONE'] != $cPhoneNum) {

        // Check for duplication of email
        $stmt = $pdo->prepare("SELECT * FROM user WHERE USER_EMAIL = ? AND USER_ID != ?");
        $stmt->execute([$cEmail, $user_id]);
        $existingEmail = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check for duplication of phone number
        $stmt = $pdo->prepare("SELECT * FROM user WHERE USER_PHONE = ? AND USER_ID != ?");
        $stmt->execute([$cPhoneNum, $user_id]);
        $existingPhone = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$existingEmail && !$existingPhone) {
            // Update user profile in the database
            $stmt = $pdo->prepare("UPDATE user SET USER_FNAME = ?, USER_LNAME = ?, USER_EMAIL = ?, USER_ADDRESS = ?, USER_PHONE = ? WHERE USER_ID = ?");
            $stmt->execute([$cFirst, $cLast, $cEmail, $cAddress, $cPhoneNum, $user_id]);
            
            // Redirect back to userProfile.php after successful update
            header("Location: userProfile.php");
            exit();
        } else {
            if ($existingEmail) {
                echo "<script>alert('Error: Email already exists. Please choose a different one.'); window.location='userProfile.php';</script>";
                exit();
            } 
            if ($existingPhone) {
                echo "<script>alert('Error: Phone number already exists. Please choose a different one.'); window.location='userProfile.php';</script>";
                exit();
            }
        }
    } else {
        // If no changes were made, do nothing
        header("Location: userProfile.php");
        exit();
    }
?>
