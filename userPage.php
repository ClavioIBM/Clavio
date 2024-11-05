<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: logPage.php");
    exit();
}

require "amfcConx.php";

$user_id = $_SESSION['user_id'];
$sqlPendingOrders = "SELECT * FROM orderprod WHERE USER_ID = :user_id AND ORDER_STATUS = 2";
$stmtPendingOrders = $pdo->prepare($sqlPendingOrders);
$stmtPendingOrders->bindParam(':user_id', $user_id);
$stmtPendingOrders->execute();
$pendingOrders = $stmtPendingOrders->fetchAll(PDO::FETCH_ASSOC);
$hasPendingOrders = count($pendingOrders) > 0;

$sql = "SELECT * FROM user WHERE USER_ID = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .header-section {
            background-image: url('images/foodpic.jpg');
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

        .about-us-section {
            background: #f8f9fa;
            padding: 60px 20px;
        }

        .about-us-section h2 {
            margin-bottom: 40px;
        }

        .about-us-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .about-us-card img {
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .image-header {
            background-image: url('images/footer-image.jpg');
            /* Replace with your footer image path */
            background-size: cover;
            background-position: center;
            height: 300px;
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
                <a href="userProfile.php" class="text-black font-bold">Account</a>
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
        <h1 class="text-4xl font-bold mb-4">Welcome to Our Restaurant</h1>
        <p class="mb-8">Delicious food just a click away</p>
    </div>

    <!-- Main Content -->
    <div class="container container-custom mt-5">
        <h1 class="mb-4">Magandang Araw, <?php echo htmlspecialchars($row["USER_FNAME"] . " " . htmlspecialchars($row["USER_LNAME"])); ?></h1>
        <form id="wf-form-orderBtn" name="wf-form-orderBtn" action="userOrder.php" method="post">
            <input type="hidden" id="uID" name="uID" value="<?php echo $user_id; ?>">
            <label for="name" class="h5">Order Now!</label>
            <hr>
            <?php if ($hasPendingOrders) : ?>
                <input type="button" class="btn btn-warning mt-2 btn-custom" value="&gt;&gt; ORDER &lt;&lt;" onclick="showModal();" />
            <?php else : ?>
                <input type="submit" name="order" class="btn btn-success mt-2 btn-custom" value="&gt;&gt; ORDER &lt;&lt;" />
            <?php endif; ?>
        </form>
    </div>

    <!-- About Section -->
    <div class="about-us-section">
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold text-center mb-8">About Us</h2>
            <div class="about-us-card">
                <div class="flex flex-wrap md:flex-nowrap">
                    <div class="w-full md:w-1/3 p-4">
                        <img src="images/ate maan.jpg" alt="About Ate Maan" class="w-full h-auto"> <!-- Replace with your about image path -->
                    </div>
                    <div class="w-full md:w-2/3 p-4">
                        <p class="mb-4">Ate Maan's is dedicated to providing delicious, home-cooked meals made with love. Our mission is to bring the taste of Filipino cuisine to your table, offering a variety of dishes that will satisfy your cravings.</p>
                        <p class="mb-4">Founded in 2019, Ate Maan's has been a beloved staple in the community, known for its quality ingredients and friendly service. We pride ourselves on creating a welcoming environment where every customer feels like family.</p>
                        <p class="mb-4">Whether you're looking for a hearty breakfast, a satisfying lunch, or a delightful dinner, Ate Maan's has something for everyone. Come and experience the flavors of the Philippines at Ate Maan's!</p>
                    </div>
                </div>
            </div>
            <div class="flex justify-center space-x-4 mt-8">
                <img src="images/GCashLogo.png" alt="GCash Payment" class="h-20"> <!-- Replace with your GCash image path -->
                <img src="https://th.bing.com/th/id/R.dcb36286f13d1a8b8406187aca32a32c?rik=mKdRb%2fdqzJlzvg&riu=http%3a%2f%2feagleexpressghana.com%2fwp-content%2fuploads%2f2017%2f02%2fBigRock-COD.png&ehk=vmOoPnmlH%2b%2b44iuoNktN3bMYLOXdjuH0l9Jqk1O9a0Q%3d&risl=&pid=ImgRaw&r=0" alt="Cash Payment" class="h-20"> <!-- Replace with your Cash image path -->
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pending Orders</h5>
                    <button type="button" class="close" onclick="hideModal();">&times;</button>
                </div>
                <div class="modal-body">
                    <p>You have pending orders. Please wait for your previous order to be completed before placing a new one.<br>
                        View your order <a href="userViewOrders.php">here</a>.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="hideModal();">Close</button>
                </div>
            </div>
        </div>
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
    <script>
        function showModal() {
            $('#myModal').modal('show');
        }

        function hideModal() {
            $('#myModal').modal('hide');
        }
    </script>
</body>

</html>