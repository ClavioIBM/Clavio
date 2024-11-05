<?php
session_start();
require "amfcConx.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: logPage.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Set default sorting option
$sort_option = isset($_GET['sort']) ? $_GET['sort'] : 'prod_id';

// Prepare SQL statement with sorting
$sql = "SELECT * FROM product ORDER BY ";
switch ($sort_option) {
    case 'prod_name':
        $sql .= "PROD_NAME";
        break;
    case 'timestamp':
        $sql .= "PROD_DATETIME DESC";
        break;
    case 'prod_id':
        $sql .= "PROD_ID";
        break;
    case 'availability':
        $sql .= "PROD_AVAIL";
        break;
    default:
        $sql .= "PROD_ID";
        break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Report</title>
    <style>
        /* Your CSS styles here */
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
        button {
            display: none; /* Hide the sort button */
        }
    </style>
</head>
<body>
    <h1>Product Report</h1>
    
    <!-- Sorting form -->
    <form id="sortForm" method="GET" action="adminProductReport.php">
        <label for="sort">Sort by:</label>
        <select name="sort" id="sort">
            <option value="prod_id" <?php echo $sort_option == 'prod_id' ? 'selected' : ''; ?>>Product ID</option>
            <option value="prod_name" <?php echo $sort_option == 'prod_name' ? 'selected' : ''; ?>>Product Name</option>
            <option value="timestamp" <?php echo $sort_option == 'timestamp' ? 'selected' : ''; ?>>Timestamp</option>
            <option value="availability" <?php echo $sort_option == 'availability' ? 'selected' : ''; ?>>Availability</option>
        </select>
        <!-- No need for the sort button -->
    </form>
    <br>
    <a href="adminGenerateReport.php" class="back-button">Back</a>
    <table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Product Price</th>
                <th>Availability</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if ($rows) {
                    foreach ($rows as $row) {
                        echo "<tr>";
                        echo "<td>" . $row['PROD_ID'] . "</td>";
                        echo "<td>" . $row['PROD_NAME'] . "</td>";
                        echo "<td>" . $row['PROD_PRICE'] . "</td>";
                        echo "<td>" . ($row['PROD_AVAIL'] == 1 ? 'Available' : 'Not Available') . "</td>";
                        echo "<td>" . $row['PROD_DATETIME'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No products found.</td></tr>";
                }
            ?>
        </tbody>
    </table>
    
    <!-- Back button -->
    
    <!-- Your JavaScript code here -->
    <script>
        document.getElementById('sort').addEventListener('change', function() {
            document.getElementById('sortForm').submit();
        });
    </script>
</body>
</html>
