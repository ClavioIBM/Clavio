<?php
    session_start();
    require "amfcConx.php"; // Assuming you have a PDO connection file

    if (!isset($_SESSION['user_id'])) {
        header("Location: logPage.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Prepare the SQL query
    $sql = "SELECT * FROM user WHERE USER_ID = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Account</title>
  <link rel="stylesheet" href="styles.css">
</head>
<style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    padding-top:100px;
    background-color: #dfdb62;
    }

    .container {
    max-width: 500px;
    margin: 50px auto;
    padding: 20px;
    border: 2px solid #ccc;
    border-radius: 5px;
    background-color: white;
    }

    h1 {
    text-align: center;
    font-family: 'Copperplate';
	font-weight: bold;
	font-style: italic;
    color: #000;
    text-shadow: 1px 1px 2px black;
    }

    .form-group {
    margin-bottom: 15px;
    }

    label {
    display: block;
    margin-bottom: 5px;
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="password"] {
    width: 95%;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    }

    button ,
    input[type="submit"]{
    width: 100%;
    padding: 10px;
    background-color: #4CAF50;
    border: none;
    color: white;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px;
    }

    button:hover {
    background-color: #45a049;
    }
</style>
<body>
  <div class="container">
    <h1>Change Password</h1>
    <div class="account-info">
    <form method="POST" action="userChangePassSubmit.php">
        <input type="hidden" id="uID" name="uID" value="<?php echo $uID; ?>">
        <div class="form-group">
            <label for="oldpass">Old Password:</label>
            <input type="password" id="oldpass" name="oldpass">
        </div>
        <div class="form-group">
            <label for="newpass">New Password:</label>
            <input type="password" id="newpass" name="newpass"required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Password must be at least 8 characters long, containing at least one uppercase letter, one lowercase letter, and one number">
        </div>
        <div class="form-group">
            <label for="confirmpass">Confirm Password:</label>
            <input type="password" id="confirmpass" name="confirmpass" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Password must be the same">
        </div>
        <input type="submit" name="updatePass" value="Update Password">
    </form><br>
    <form method="POST" action="userPage.php">
        <input type="hidden" id="uID" name="uID" value="<?php echo $uID; ?>">
        <input type="submit" name="back" value="Back">
    </form><br>
  </div>
</body>
</html>
