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
            $orderBy = "USER_ID";
            break;
        case 'fname':
            $orderBy = "USER_FNAME";
            break;
        case 'lname':
            $orderBy = "USER_LNAME";
            break;
        case 'address':
            $orderBy = "USER_ADDRESS";
            break;
        case 'payment_method': // New case for sorting by payment method
            $orderBy = "pm.PAY_METHOD";
            break;
        default:
            $orderBy = "USER_ID"; // Default sorting by GROUP_ID
            break;
    }

    // SQL query to group orders by GROUP_ID and concatenate order IDs, product names, quantities, and prices
    $sql = "SELECT * FROM user WHERE USER_LEVEL = 2";


    $sql .= "
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
    <title>User Report</title>
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
    <h1>User Report</h1>
     <!-- Sorting form -->
     <form id="sortForm" method="GET" action="adminUserList.php">
        <label for="sort">Sort by:</label>
        <select name="sort" id="sort">
            <option value="user_id" <?php echo $sort_option == 'user_id' ? 'selected' : ''; ?>>User ID</option>
            <option value="fname" <?php echo $sort_option == 'fname' ? 'selected' : ''; ?>>First Name</option>
            <option value="lname" <?php echo $sort_option == 'lname' ? 'selected' : ''; ?>>Last Name</option>
        </select>
    </form>
    <a href="adminGenerateReport.php" class="back-button">Back</a>
    <table>
        <thead>
            <tr>  
                <th>User ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if ($rows) {
                    foreach ($rows as $row) {
                        echo "<tr>";
                        echo "<td>" . $row['USER_ID'] . "</td>";
                        echo "<td>" . $row['USER_FNAME'] . "</td>";
                        echo "<td>" . $row['USER_LNAME'] . "</td>";
                        echo "<td>" . $row['USER_ADDRESS'] . "</td>"; // Assuming USER_ADDRESS is a valid column
                        echo "<td>" . $row['USER_PHONE'] . "</td>";
                        echo "<td>" . $row['USER_EMAIL'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11'>No users. found.</td></tr>"; // Adjusted colspan to match the number of columns displayed
                }
            ?>
        </tbody>
    </table>

    <!-- Your JavaScript code here -->
    <script>
        document.getElementById('sort').addEventListener('change', function() {
            document.getElementById('sortForm').submit();
        });
    </script>
</body>
</html>
