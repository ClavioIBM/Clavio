<?php
    session_start();
    require "amfcConx.php";

    if (!isset($_SESSION['user_id'])) {
        header("Location: logPage.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Set default sorting option to 'group_id'
    $sort_option = isset($_GET['sort']) ? $_GET['sort'] : 'group_id';

    // Prepare the SQL statement with sorting
    switch ($sort_option) {
        case 'user_id':
            $orderBy = "u.USER_ID";
            break;
        case 'fname':
            $orderBy = "u.USER_FNAME";
            break;
        case 'lname':
            $orderBy = "u.USER_LNAME";
            break;
        case 'payment_datetime':
            $orderBy = "pm.PAY_DATETIME DESC"; // Order by payment datetime in descending order
            break;
        case 'payment_method':
            $orderBy = "pm.PAY_METHOD";
            break;
        case 'group_id':
        default:
            $orderBy = "o.GROUP_ID"; // Default sorting by GROUP_ID
            break;
    }

    // SQL query to group orders by GROUP_ID and concatenate order IDs, product names, quantities, and prices
    $sql = "SELECT 
                o.GROUP_ID,
                GROUP_CONCAT(o.ORDER_ID ORDER BY o.ORDER_ID SEPARATOR '<br>') AS ORDER_IDS,
                u.USER_ID, 
                u.USER_FNAME, 
                u.USER_LNAME, 
                u.USER_ADDRESS,
                GROUP_CONCAT(p.PROD_NAME ORDER BY o.ORDER_ID SEPARATOR '<br>') AS PRODUCT_NAMES,
                GROUP_CONCAT(o.ORDER_QUANTITY ORDER BY o.ORDER_ID SEPARATOR '<br>') AS QUANTITIES,
                GROUP_CONCAT(o.ORDER_TOTALAMOUNT ORDER BY o.ORDER_ID SEPARATOR '<br>') AS PRICES,
                SUM(o.ORDER_TOTALAMOUNT) AS TOTAL_AMOUNT,
                o.ORDER_STATUS,
                pm.PAY_METHOD,
                pm.PAY_DATETIME
            FROM 
                ORDERPROD o
            INNER JOIN 
                USER u ON o.USER_ID = u.USER_ID
            INNER JOIN 
                PRODUCT p ON o.PROD_ID = p.PROD_ID
            LEFT JOIN 
                PAYMENT pm ON o.ORDER_ID = pm.ORDER_ID
            WHERE 
                o.ORDER_STATUS = 1";

    // If sorting by payment method and a specific payment method is selected, filter by that payment method
    if ($sort_option == 'payment_method' && isset($_GET['payment_method_filter']) && $_GET['payment_method_filter'] !== 'all') {
        $payment_method_filter = $_GET['payment_method_filter'];
        $sql .= " AND pm.PAY_METHOD = ?";
        $params[] = $payment_method_filter;
    }

    $sql .= " GROUP BY 
                o.GROUP_ID
            ORDER BY 
                $orderBy";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(isset($params) ? $params : null);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            margin-right: 10px;
        }

        select {
            padding: 5px;
            border-radius: 5px;
        }

        button {
            padding: 5px 10px;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .back-button {
            margin-top: 20px;
            padding: 10px 10px;
            font-size: 12px;
            border: none;
            border-radius: 5px;
            background-color: #e93737;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: inline-block;
            text-decoration: none;
        }

        .back-button:hover {
            background-color: #c53131;
        }
    </style>
</head>
<body>
    <h1>Order Report</h1>
    <!-- Sorting form -->
    <form id="sortForm" method="GET" action="adminOrderReport.php">
        <label for="sort">Sort by:</label>
        <select name="sort" id="sort">
            <option value="group_id" <?php echo $sort_option == 'group_id' ? 'selected' : ''; ?>>Group ID</option>
            <option value="user_id" <?php echo $sort_option == 'user_id' ? 'selected' : ''; ?>>User ID</option>
            <option value="fname" <?php echo $sort_option == 'fname' ? 'selected' : ''; ?>>First Name</option>
            <option value="lname" <?php echo $sort_option == 'lname' ? 'selected' : ''; ?>>Last Name</option>
            <option value="payment_method" <?php echo $sort_option == 'payment_method' ? 'selected' : ''; ?>>Payment Method</option>
            <option value="payment_datetime" <?php echo $sort_option == 'payment_datetime' ? 'selected' : ''; ?>>Payment Datetime</option>
        </select>
    </form>
    <a href="adminGenerateReport.php" class="back-button">Back</a>
    <table>
        <thead>
            <tr>
                <th>Group ID</th>
                <th>User ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Address</th>
                <th>Product Names</th>
                <th>Quantities</th>
                <th>Prices</th>
                <th>Total Amount</th>
                <th>Payment Method</th>
                <th>Payment Date</th> <!-- New column for Payment Date -->
                <th>Order Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if ($rows) {
                    foreach ($rows as $row) {
                        echo "<tr>";
                        echo "<td>" . $row['GROUP_ID'] . "</td>";
                        echo "<td>" . $row['USER_ID'] . "</td>";
                        echo "<td>" . $row['USER_FNAME'] . "</td>";
                        echo "<td>" . $row['USER_LNAME'] . "</td>";
                        echo "<td>" . $row['USER_ADDRESS'] . "</td>"; // Assuming USER_ADDRESS is a valid column
                        echo "<td>" . $row['PRODUCT_NAMES'] . "</td>";
                        echo "<td>" . $row['QUANTITIES'] . "</td>";
                        echo "<td>" . $row['PRICES'] . "</td>";
                        echo "<td>" . $row['TOTAL_AMOUNT'] . "</td>";
                        $payment_method = $row['PAY_METHOD'] == 1 ? "Cash" : "GCash";
                        echo "<td>" . $payment_method . "</td>";
                        echo "<td>" . $row['PAY_DATETIME'] . "</td>"; // New column data for Payment Date
                        $order_status = $row['ORDER_STATUS'] == 1 ? "Completed" : "Pending";
                        echo "<td>" . $order_status . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='12'>No orders found.</td></tr>"; // Adjusted colspan to match the number of columns displayed
                }
            ?>
        </tbody>
    </table>

    <script>
        document.getElementById('sort').addEventListener('change', function() {
            document.getElementById('sortForm').submit();
        });
    </script>
</body>
</html>