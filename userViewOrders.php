<?php
session_start();
require "amfcConx.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: logPage.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$resultOrderInfo = []; // Initialize the variable to avoid undefined variable warning

$order_type = 'pending'; // Default order type

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_type'])) {
    $order_type = $_POST['order_type'];
}

// Fetch order information along with product details based on order type
if ($order_type == 'completed') {
    $sqlOrderInfo = "SELECT 
                        op.GROUP_ID,
                        GROUP_CONCAT(op.ORDER_ID SEPARATOR '<br>') AS order_ids,
                        GROUP_CONCAT(p.PROD_ID SEPARATOR '<br>') AS prod_ids,
                        GROUP_CONCAT(p.PROD_NAME SEPARATOR '<br>') AS prod_names,
                        IF(COUNT(DISTINCT op.ORDER_ID) > 1, GROUP_CONCAT(op.ORDER_QUANTITY SEPARATOR '<br>'), SUM(op.ORDER_QUANTITY)) AS ORDER_QUANTITY,
                        GROUP_CONCAT(CONCAT(p.PROD_PRICE) SEPARATOR '<br>') AS PROD_PRICE,
                        CONCAT(SUM(op.ORDER_QUANTITY * p.PROD_PRICE) + 30) AS total_amount,
                        MAX(op.ORDER_DATETIME) AS ORDER_DATETIME
                    FROM 
                        orderprod op
                    INNER JOIN 
                        product p ON op.PROD_ID = p.PROD_ID
                    WHERE 
                        op.USER_ID = :user_id AND op.ORDER_STATUS = 1
                    GROUP BY 
                        op.GROUP_ID
                    ORDER BY 
                        op.GROUP_ID DESC";
} else {
    $sqlOrderInfo = "SELECT 
                        op.GROUP_ID,
                        GROUP_CONCAT(op.ORDER_ID SEPARATOR '<br>') AS order_ids,
                        GROUP_CONCAT(p.PROD_ID SEPARATOR '<br>') AS prod_ids,
                        GROUP_CONCAT(p.PROD_NAME SEPARATOR '<br>') AS prod_names,
                        IF(COUNT(DISTINCT op.ORDER_ID) > 1, GROUP_CONCAT(op.ORDER_QUANTITY SEPARATOR '<br>'), SUM(op.ORDER_QUANTITY)) AS ORDER_QUANTITY,
                        GROUP_CONCAT(CONCAT(p.PROD_PRICE) SEPARATOR '<br>') AS PROD_PRICE,
                        CONCAT(SUM(op.ORDER_QUANTITY * p.PROD_PRICE) + 30) AS total_amount,
                        MAX(op.ORDER_DATETIME) AS ORDER_DATETIME
                    FROM 
                        orderprod op
                    INNER JOIN 
                        product p ON op.PROD_ID = p.PROD_ID
                    WHERE 
                        op.USER_ID = :user_id AND op.ORDER_STATUS = 2
                    GROUP BY 
                        op.GROUP_ID
                    ORDER BY 
                        op.GROUP_ID DESC";
}

$stmt = $pdo->prepare($sqlOrderInfo);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$resultOrderInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
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

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            text-align: center;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }
    </style>
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="#">
                <img src="images/ate_maan.jpg" alt="Ate Maan's Logo" class="h-10"> <!-- Replace with your logo path -->
            </a>
            <div class="flex space-x-4 items-center">
                <a href="userPage.php" class="text-black font-bold">Home</a>
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
        <h1 class="text-4xl font-bold mb-4">Order History</h1>
        <p class="mb-8">View your past and current orders</p>
    </div>

    <!-- Main Content -->
    <div class="container container-custom mt-5">
        <h1 class="text-3xl font-bold text-center mb-8">User Order History</h1>
        <?php
        // Determine the text content of the h2 element based on the order type
        $h2_text = ($order_type == 'completed') ? "Completed Orders" : "Pending Orders";
        ?>
        <h2 class="text-2xl font-bold text-center mb-4"><?php echo $h2_text; ?></h2>
        <div class="flex justify-between mb-4">
            <button class="bg-red-500 text-white px-4 py-2 rounded transition-all hover:bg-red-600 font-bold" onclick="window.location.href = 'userPage.php';">Back</button>
            <form method="post">
                <?php if ($order_type == 'completed') : ?>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded transition-all hover:bg-blue-600 font-bold" name="order_type" value="pending">Pending Orders</button>
                <?php else : ?>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded transition-all hover:bg-blue-600 font-bold" name="order_type" value="completed">Completed Orders</button>
                <?php endif; ?>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">Product Names</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">Date & Time</th>
                        <?php if ($order_type == 'pending') : ?>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php foreach ($resultOrderInfo as $rowOrderInfo) : ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500"><?php echo $rowOrderInfo['GROUP_ID']; ?></td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500"><?php echo $rowOrderInfo['prod_names']; ?></td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500"><?php echo $rowOrderInfo['ORDER_QUANTITY']; ?></td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500"><?php echo $rowOrderInfo['PROD_PRICE']; ?></td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">₱<?php echo $rowOrderInfo['total_amount']; ?></td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500"><?php echo $rowOrderInfo['ORDER_DATETIME']; ?></td>
                            <?php if ($order_type == 'pending') : ?>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                    <button class="bg-yellow-500 text-white px-4 py-2 rounded transition-all hover:bg-yellow-600 font-bold" onclick="openModal(
                                        '<?php echo $rowOrderInfo['GROUP_ID']; ?>',
                                        '<?php echo $rowOrderInfo['prod_names']; ?>',
                                        '<?php echo $rowOrderInfo['ORDER_QUANTITY']; ?>',
                                        '<?php echo $rowOrderInfo['PROD_PRICE']; ?>',
                                        '₱<?php echo $rowOrderInfo['total_amount']; ?>',
                                        '<?php echo $rowOrderInfo['ORDER_DATETIME']; ?>'
                                    )">View Info</button>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Order Info</h2>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">Product Names</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">Quantities</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">Prices</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">Total Amount</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500" id="orderIds"></td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500" id="prodNames"></td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500" id="quantities"></td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500" id="prices"></td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500" id="totalAmount"></td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500" id="dateTime"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer -->
    

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openModal(orderIds, prodNames, quantities, prices, totalAmount, dateTime) {
            var modal = document.getElementById("myModal");
            var span = document.getElementsByClassName("close")[0];

            // Set content into respective table cells
            document.getElementById("orderIds").innerHTML = orderIds;
            document.getElementById("prodNames").innerHTML = prodNames;
            document.getElementById("quantities").innerHTML = quantities;
            document.getElementById("prices").innerHTML = prices;
            document.getElementById("totalAmount").innerHTML = totalAmount;
            document.getElementById("dateTime").innerHTML = dateTime;

            modal.style.display = "block";

            span.onclick = function() {
                modal.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        }
    </script>
</body>

</html>