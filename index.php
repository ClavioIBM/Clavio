<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ate Maan's Food Order System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .header-section {
            background-image: url('./images/home_bg.jpg');
            /* Replace with your image path */
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 100px 20px;
        }

        .header-buttons a {
            margin: 10px;
        }

        .service-section {
            background-color: #fdd835;
            padding: 40px 20px;
        }

        .service-section h2 {
            margin-bottom: 40px;
        }

        .footer-section {
            background-color: white;
            color: black;
            font-weight: bold;
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="#">
                <img src="./images/ate maan.jpg" alt="Ate Maan's Logo" class="h-10"> <!-- Replace with your logo path -->
            </a>
            <div>
                <a href="logPage.php" class="bg-yellow-500 text-white px-4 py-2 rounded transition-all hover:bg-yellow-600">Login</a>
            </div>
        </div>
    </nav>

    <!-- Header Section -->
    <div class="header-section">
        <h1 class="text-4xl font-bold mb-4">Ate Maan's Food Corner</h1>
        <p class="mb-8">We accept orders for all occasions!</p>
    </div>

    <!-- Service Section -->
    <div class="service-section text-center">
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold mb-8">Our Services</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
                <div>
                    <img src="./images/quality_food_img.png" alt="Quality" class="mx-auto mb-4"> <!-- Replace with your icon -->
                    <h3 class="text-xl font-bold mb-2">Quality Platter Foods</h3>
                    <p>Indulge in our exquisite platter dishes like our signature spaghetti and flavorful cordon bleu.</p>
                </div>
                <div>
                    <img src="./images/fast_delivery_img.png" alt="Delivery" class="mx-auto mb-4"> <!-- Replace with your icon -->
                    <h3 class="text-xl font-bold mb-2">Fast Delivery</h3>
                    <p>Enjoy the convenience of swift delivery without compromising on the freshness and quality of our dishes.</p>
                </div>
                <div>
                    <img src="./images/quality_food_img.png" alt="Flavors" class="mx-auto mb-4"> <!-- Replace with your icon -->
                    <h3 class="text-xl font-bold mb-2">Original Flavors</h3>
                    <p>Experience the authentic taste of Filipino cuisine at Ate Maan Restaurant, reflecting our dedication to preserving original flavors.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container mx-auto p-8 bg-white border border-gray-300 rounded shadow-lg mt-8">
        <h1 class="text-center text-3xl font-bold mb-8 text-gray-800">Ate Maan's Food Order System</h1>

        <?php
        // Database connection and query
        require "amfcConx.php"; // Include your database connection file

        try {
            // Assuming your table is named 'products'
            $sql = "SELECT * FROM product ORDER BY PROD_CATEGORY, PROD_NAME";
            $stmt = $pdo->query($sql);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Initialize a variable to keep track of category changes
            $currentCategory = null;

            // Loop through products and display them
            foreach ($products as $product) {
                // Check if category has changed
                if ($product['PROD_CATEGORY'] !== $currentCategory) {
                    // Display category heading
                    if ($currentCategory !== null) echo '</div>'; // Close previous category container
                    echo '<div class="mt-8 mb-4 text-xl font-bold text-gray-700">' . htmlspecialchars($product['PROD_CATEGORY']) . '</div>';
                    echo '<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">'; // New category container
                    // Update current category
                    $currentCategory = $product['PROD_CATEGORY'];
                }

                // Display product
                $imgSrc = "images/img" . $product['PROD_ID'] . ".png";
        ?>
                <div class="product bg-white border border-gray-300 rounded overflow-hidden transition-shadow hover:shadow-lg flex flex-col">
                    <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($product['PROD_NAME']); ?>" class="w-full h-auto">
                    <div class="product-info p-4 flex-grow flex flex-col justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($product['PROD_NAME']); ?></h2>
                            <p class="text-md text-gray-600 mb-4">₱<?php echo number_format($product['PROD_PRICE'], 2); ?></p>
                        </div>
                        <a href="logPage.php" class="block w-full text-center bg-yellow-500 text-white py-2 rounded transition-all hover:bg-yellow-600 mt-auto">Order Now</a>
                    </div>
                </div>
        <?php
            }
            echo '</div>'; // Close the last category container
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        ?>
    </div>

    <!-- Footer -->
    <footer class="footer-section p-4 mt-8">
        <div class="container mx-auto text-center">
            <p>&copy; 2024 Ate Maan's Food Order System. All rights reserved.</p>
            <p>📞 0936-1733-019 | 📞 0991-8711-100</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>