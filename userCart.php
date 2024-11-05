<?php
session_start();
require "amfcConx.php";

if (!isset($_SESSION['user_id'])) {
  header("Location: logPage.php");
  exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT GROUP_ID, ORDER_ID FROM orderprod WHERE USER_ID = :user_id AND ORDER_STATUS = 2";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$rowOrdProd = $stmt->fetch(PDO::FETCH_ASSOC);

// Set GROUP_ID and ORDER_ID in session
$group_id = $rowOrdProd['GROUP_ID'];
$_SESSION['group_id'] = $group_id;

$order_id = $rowOrdProd['ORDER_ID'];
$_SESSION['order_id'] = $order_id;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    .container-custom {
      background: rgba(255, 255, 255, 0.9);
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      margin-top: 20px;
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

    .cart-item {
      border-bottom: 1px solid #dddfe2;
      padding: 20px;
      background-color: #fff;
      display: flex;
      align-items: center;
    }

    .cart-item img {
      width: 150px;
      height: auto;
      border-radius: 5px;
      margin-right: 20px;
    }

    .cart-item h2 {
      color: #1d2129;
      font-size: 18px;
      font-weight: 600;
      margin: 0;
      flex-grow: 1;
    }

    .cart-item p {
      font-weight: 400;
      color: #606770;
      font-size: 16px;
      margin: 0;
    }

    .cart-total {
      color: #1d2129;
      font-size: 20px;
      font-weight: 600;
      text-align: right;
      margin-top: 20px;
    }

    .quantity {
      display: flex;
      align-items: center;
    }

    .quantity input[type="submit"] {
      background-color: #ffc107;
      border: none;
      color: white;
      padding: 5px 10px;
      text-align: center;
      text-decoration: none;
      font-size: 16px;
      cursor: pointer;
      border-radius: 5px;
      margin-right: 5px;
    }

    .quantity input {
      width: 50px;
      text-align: center;
      margin-right: 5px;
      border: 1px solid #dddfe2;
      border-radius: 5px;
    }

    .total {
      font-weight: bold;
      float: right;
    }

    .checkout-button {
      background-color: #28a745;
      border: none;
      color: white;
      padding: 10px 20px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 16px;
      margin-top: 20px;
      cursor: pointer;
      border-radius: 5px;
      width: 150px;
    }

    .checkout-button:hover {
      background-color: #218838;
    }

    .returnOrder-page {
      background-color: #dc3545;
      border: none;
      color: white;
      padding: 10px 20px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 16px;
      margin-top: 20px;
      cursor: pointer;
      border-radius: 5px;
      width: 150px;
    }

    .returnOrder-page:hover {
      background-color: #c82333;
    }

    .remove-button {
      background-color: #dc3545;
      color: white;
      padding: 10px 20px;
      text-align: center;
      font-size: 16px;
      margin-top: 10px;
      cursor: pointer;
      border-radius: 5px;
      border: none;
      width: 150px;
    }

    .remove-button:hover {
      background-color: #c82333;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
      background-color: #fff;
      margin: 15% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 40%;
      border-radius: 5px;
    }

    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }

    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    th,
    td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }
  </style>
</head>

<body>
  <div class="container container-custom">
    <h1 class="text-2xl font-bold mb-4 text-center">Your Cart</h1>
    <?php
    // Initialize total amount variable
    $totalAmount = 0;

    // Initialize an array to store grouped products
    $groupedProducts = array();

    // SQL query to retrieve cart items grouped by category
    $sql = "SELECT op.*, p.*
                FROM orderprod op
                JOIN product p ON op.PROD_ID = p.PROD_ID
                WHERE op.USER_ID = :user_id AND op.ORDER_STATUS = 2";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    // Group products by category
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $category = $row['PROD_CATEGORY'];
      if (!isset($groupedProducts[$category])) {
        $groupedProducts[$category] = array();
      }
      $groupedProducts[$category][] = $row;

      // Calculate total amount
      $totalAmount += $row['ORDER_TOTALAMOUNT'];
    }

    // Display cart items grouped by category
    foreach ($groupedProducts as $category => $products) {
      echo "<h2 class='text-xl font-bold mb-2'>$category</h2>";
      foreach ($products as $product) {
        echo "<div class='cart-item flex items-center mb-4'>";
        echo "<img src='images/img{$product['PROD_ID']}.png' class='w-40 h-auto rounded-lg mr-4'>";
        echo "<div class='flex-grow'>";
        echo "<h2 class='text-lg font-bold mb-2'>{$product['PROD_NAME']}</h2>";
        echo "<p class='text-gray-700 mb-2'>₱" . number_format($product['PROD_PRICE'], 2) . "</p>";
        echo "<div class='quantity flex items-center mb-2'>";
        echo "<form method='POST' action='userQuantiUp.php' class='flex items-center'>";
        echo "<input type='hidden' name='user_id' value='$user_id'>";
        echo "<input type='hidden' name='prod' value='{$product['PROD_ID']}'>";
        if ($product['ORDER_QUANTITY'] > 1) {
          echo "<input type='submit' name='minus' value='-' class='btn btn-warning'>";
        } else {
          echo "<input type='submit' name='minus' value='-' class='btn btn-warning' disabled>";
        }
        echo "<input type='text' name='quantity' value='{$product['ORDER_QUANTITY']}' readonly class='mx-2 w-12 text-center border border-gray-300 rounded-lg'>";
        echo "<input type='hidden' name='quantiUp' value='add'>";
        echo "<input type='submit' name='add' value='+' class='btn btn-warning'>";
        echo "</form>";
        echo "</div>"; // Close quantity div
        echo "<p class='text-gray-700'>Total: ₱" . number_format($product['ORDER_TOTALAMOUNT'], 2) . "</p>";
        echo "<form method='POST' action='userRemoveFromCart.php'>";
        echo "<input type='hidden' name='ord_status' value='{$product['ORDER_STATUS']}'>";
        echo "<input type='hidden' name='prod_id' value='{$product['PROD_ID']}'>";
        echo "<input type='submit' class='remove-button' value='Remove from Cart'>";
        echo "</form>";
        echo "</div>"; // Close product div
        echo "</div>"; // Close cart-item div
      }
    }
    ?>
    <!-- Display total amount -->
    <div class='cart-total text-right font-bold'>
      <h2>Total Amount: ₱<?php echo number_format($totalAmount + 30, 2) ?></h2>
      <h4 class='text-gray-700'>*Delivery Fee of ₱30 included*</h4>
    </div>
    <div class="flex justify-end gap-4">
      <?php
      // Display checkout button if there are items in the cart
      if ($totalAmount > 0) {
        echo "<button class='checkout-button' onclick='openModal()'>Checkout</button>";
      }
      ?>
      <a href="userOrder.php" class='returnOrder-page'>Return to Order Page</a>
    </div>
  </div>

  <!-- Modal for checkout confirmation -->
  <div id="myModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h2>Checkout</h2>
      <p>Do you want to proceed with the checkout?</p>
      <?php
      // Display cart items for checkout
      if ($totalAmount > 0) {
        echo "<table>";
        echo "<tr><th>Product</th><th>Quantity</th><th>Price</th><th>Total</th></tr>";
        foreach ($groupedProducts as $category => $products) {
          foreach ($products as $product) {
            echo "<tr>";
            echo "<td>{$product['PROD_NAME']}</td>";
            echo "<td>{$product['ORDER_QUANTITY']}</td>";
            echo "<td>₱" . number_format($product['PROD_PRICE'], 2) . "</td>";
            echo "<td>₱" . number_format($product['ORDER_TOTALAMOUNT'], 2) . "</td>";
            echo "</tr>";
          }
        }
        echo "</table>";
        echo "<p>Total Amount: ₱" . number_format($totalAmount + 30, 2) . "</p>";
        echo "<p>*Delivery fee of ₱30 included*</p>";
      } else {
        echo "No items in the cart.";
      }
      ?>
      <!-- Form for actual checkout process -->
      <form method="POST" action="userPayment.php">
        <input type="hidden" name="group_id" value="<?php echo $_SESSION['group_id']; ?>">
        <input type="hidden" name="order_id" value="<?php echo $_SESSION['order_id']; ?>">
        <input type='submit' class='checkout-button' value='Proceed To Payment'>
      </form>
    </div>
  </div>

  <script>
    // JavaScript functions to open and close the modal
    function openModal() {
      document.getElementById("myModal").style.display = "block";
    }

    function closeModal() {
      document.getElementById("myModal").style.display = "none";
    }

    // Close the modal if user clicks outside of it
    window.onclick = function(event) {
      var modal = document.getElementById("myModal");
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }
  </script>
</body>

</html>