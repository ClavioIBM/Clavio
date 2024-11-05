<?php
session_start();
require "amfcConx.php";

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .header-section {
            background-image: url('images/1.jpg');
            /* Replace with your image path */
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 100px 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .header-buttons a {
            margin: 10px;
        }

        .container-custom {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: -50px;
            z-index: 1;
        }

        .footer-section {
            background-color: white;
            color: black;
            font-weight: bold;
            padding: 20px 0;
            text-align: center;
            margin-top: auto;
        }

        .btn-custom {
            display: inline-block;
            width: 100%;
            text-align: center;
        }
    </style>
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="#">
                <img src="images/ate maan.jpg" alt="Ate Maan's Logo" class="h-10"> <!-- Replace with your logo path -->
            </a>
            <div class="flex space-x-4 items-center">
                <a href="userPage.php" class="text-black font-bold">Home</a>
                <a href="userViewOrders.php" class="text-black font-bold">
                    <i class="fas fa-shopping-cart"></i>
                </a>
                <form id="wf-form-logoutBtn" name="wf-form-logoutBtn" action="amfcLogout.php" method="post" class="inline">
                    <input type="hidden" id="uID" name="uID" value="<?php echo $user_id; ?>">
                    <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded transition-all hover:bg-yellow-600 font-bold">Log Out</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Header Section -->
    <div class="header-section">
        <h1 class="text-4xl font-bold mb-4">My Profile</h1>
        <p class="mb-8">Manage your account details</p>
    </div>

    <!-- Main Content -->
    <div class="container container-custom mt-5">
        <h1 class="mb-4 text-3xl font-bold text-center">Profile Information</h1>
        <form method="POST" action="userProfileUp.php">
            <input type="hidden" id="uID" name="uID" value="<?php echo $user_id; ?>">
            <div class="form-group mb-4">
                <label for="ID" class="block text-gray-700 font-bold mb-2">ID:</label>
                <input type="text" class="form-control" id="ID" name="ID" value="<?php echo $row["USER_ID"]; ?>" disabled>
            </div>
            <div class="form-group mb-4">
                <label for="firstName" class="block text-gray-700 font-bold mb-2">First Name:</label>
                <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo $row["USER_FNAME"]; ?>" required>
            </div>
            <div class="form-group mb-4">
                <label for="lastName" class="block text-gray-700 font-bold mb-2">Last Name:</label>
                <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo $row["USER_LNAME"]; ?>" required>
            </div>
            <div class="form-group mb-4">
                <label for="email" class="block text-gray-700 font-bold mb-2">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $row["USER_EMAIL"]; ?>" required>
            </div>
            <div class="form-group mb-4">
                <label for="address" class="block text-gray-700 font-bold mb-2">Delivery Address:</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo $row["USER_ADDRESS"]; ?>" required>
            </div>
            <div class="form-group mb-4">
                <label for="phone" class="block text-gray-700 font-bold mb-2">Phone:</label>
                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $row["USER_PHONE"]; ?>" required>
            </div>
            <input type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded transition-all hover:bg-yellow-600 font-bold btn-custom" name="update" value="Update">
        </form><br>
        <form method="POST" action="userChangePass.php">
            <input type="hidden" id="uID" name="uID" value="<?php echo $user_id; ?>">
            <input type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded transition-all hover:bg-yellow-600 font-bold btn-custom" name="changepass" value="Change Password">
        </form><br>
        <form method="POST" action="userPage.php">
            <input type="hidden" id="uID" name="uID" value="<?php echo $user_id; ?>">
            <input type="submit" class="bg-red-500 text-white px-4 py-2 rounded transition-all hover:bg-red-600 font-bold btn-custom" name="back" value="Back">
        </form>
    </div>

    <!-- Footer -->
    <footer class="footer-section mt-8">
        <div class="container mx-auto text-center">
            <p>&copy; 2024 Ate Maan's. All Rights Reserved.</p>
            <p>Follow us on:
                <a href="#" class="text-black"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-black"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-black"><i class="fab fa-instagram"></i></a>
            </p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>