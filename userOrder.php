<?php
session_start();
require "amfcConx.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: logPage.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT * FROM user WHERE USER_ID = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch products grouped by category
$sql = "SELECT p.*, IFNULL(op.ORDER_QUANTITY, 0) AS cart_quantity, IF(op.USER_ID IS NULL, 0, 1) AS in_cart 
        FROM product p
        LEFT JOIN orderprod op ON p.PROD_ID = op.PROD_ID AND op.USER_ID = :user_id AND op.ORDER_STATUS = '2'
        ORDER BY p.PROD_CATEGORY, p.PROD_NAME";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$resultProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count cart items
$sqlCartItems = "SELECT COUNT(*) AS cartItems FROM orderprod WHERE USER_ID = :user_id AND ORDER_STATUS = '2'";
$stmtCartItems = $pdo->prepare($sqlCartItems);
$stmtCartItems->bindParam(':user_id', $user_id);
$stmtCartItems->execute();
$rowCartItems = $stmtCartItems->fetch(PDO::FETCH_ASSOC);
$cartItemsCount = $rowCartItems['cartItems'];

// If the cart is empty, set a variable to disable the "View Cart" button
$viewCartDisabled = ($cartItemsCount == 0) ? "disabled" : "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .header-section {
            background-image: url('images/home_bg.jpg');
            /* Replace with your image path */
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 100px 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
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

        .card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-title {
            color: black;
        }

        .btn-warning {
            background-color: #ffc107;
            color: white;
            border: none;
        }

        .btn-warning:hover {
            background-color: #e0a800;
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
                <form method="POST" action="userCart.php" class="form-inline">
                    <input type="hidden" id="uID" name="uID" value="<?= $user_id ?>">
                    <button type="submit" name="cart" class="btn btn-warning" <?= $viewCartDisabled ?>>
                        <i class="fas fa-shopping-cart"></i> My Cart
                    </button>
                </form>
                <form method="POST" action="userPage.php" class="form-inline">
                    <input type="hidden" id="uID" name="uID" value="<?= $user_id ?>">
                    <button type="submit" name="return" class="btn btn-danger ml-2">Return</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Header Section -->
    <div class="header-section">
        <h1 class="text-4xl font-bold mb-4">Menu</h1>
        <p class="mb-8">Choose from our delicious offerings</p>
    </div>

    <!-- Main Content -->
    <div class="container container-custom mt-5">
        <h1 class="text-3xl font-bold text-center mb-8">Category</h1>

        <?php
        // Initialize a variable to track current category
        $currentCategory = null;

        foreach ($resultProducts as $product) {
            if ($product['PROD_CATEGORY'] !== $currentCategory) {
                // Start a new category section
                if ($currentCategory !== null) {
                    echo '</div>'; // Close previous category div
                }
                $currentCategory = $product['PROD_CATEGORY'];
                echo '<div class="row mb-3"><div class="col"><h2 class="text-2xl font-bold">' . htmlspecialchars($currentCategory) . '</h2></div></div>'; // Display category name
                echo '<div class="row">';
            }
        ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card shadow-lg rounded-lg overflow-hidden">
                    <img src="images/img<?= $product['PROD_ID'] ?>.png" class="card-img-top" alt="<?= $product['PROD_NAME'] ?>">
                    <div class="card-body">
                        <h5 class="card-title text-xl font-bold"><?= $product['PROD_NAME'] ?></h5>
                        <p class="card-text font-bold text-gray-700">₱<?= $product['PROD_PRICE'] ?></p>
                        <form method="POST" action="userAddToCart.php">
                            <div class="form-group mb-3">
                                <label for="<?= $product['PROD_ID'] ?>Quantity" class="block text-gray-700 font-bold mb-2">Quantity:</label>
                                <input type="number" class="form-control" id="<?= $product['PROD_ID'] ?>Quantity" name="quantity" value="<?= $product['in_cart'] ? $product['cart_quantity'] : 1 ?>" min="1" <?= $product['in_cart'] ? 'disabled' : '' ?>>
                            </div>
                            <?php if ($product['in_cart']) : ?>
                                <button type="button" class="btn btn-secondary" disabled>Already in Cart</button>
                            <?php else : ?>
                                <?php if ($product['PROD_AVAIL'] == 1) : ?>
                                    <input type="hidden" id="uID" name="uID" value="<?= $_SESSION['user_id'] ?>">
                                    <input type="hidden" name="product" value="<?= $product['PROD_NAME'] ?>">
                                    <button type="submit" name="addToCart" class="btn btn-warning">Add to Cart</button>
                                <?php else : ?>
                                    <button type="button" class="btn btn-secondary" disabled>Not Available</button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        <?php
        }
        echo '</div>'; // Close the last category div
        ?>
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