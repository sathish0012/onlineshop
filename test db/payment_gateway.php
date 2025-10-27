<?php
session_start();
include "db.php";

// Redirect if user not logged in
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// Validate POST data
if (!isset($_POST['product_id'], $_POST['quantity'], $_POST['product_price'], $_POST['first_name'], $_POST['mobile'])) {
    echo "Incomplete order data!";
    exit;
}

// Save order in session
$_SESSION['order'] = [
    'product_id' => $_POST['product_id'],
    'product_name' => $_POST['product_name'],
    'product_price' => floatval($_POST['product_price']), // unit price
    'quantity' => intval($_POST['quantity']),
    'first_name' => $_POST['first_name'],
    'mobile' => $_POST['mobile'],
    'alt_mobile' => $_POST['alt_mobile'] ?? '',
    'email' => $_POST['email'] ?? '',
    'house_no' => $_POST['house_no'] ?? '',
    'full_address' => $_POST['full_address'] ?? '',
    'landmark' => $_POST['landmark'] ?? '',
    'state' => $_POST['state'] ?? '',
    'pincode' => $_POST['pincode'] ?? '',
    'payment_method' => $_POST['payment_method'] ?? ''
];

// Get order
$order = $_SESSION['order'];

// Delivery and total calculation
$unit_price = $order['product_price'];
$quantity = $order['quantity'];
$delivery_charge = 50; // fixed
$total_price = $unit_price + $delivery_charge; // ✅ Only unit price + delivery charge
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Summary</title>
<style>
body { font-family: Arial; background: #fce4ec; margin: 0; padding: 0; }
.container { max-width: 500px; margin: 50px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
h2 { text-align: center; color: #c2185b; margin-bottom: 20px; }
p { margin: 8px 0; }
.total { font-weight: bold; margin-top: 15px; }
button { padding: 12px 20px; background: #f06292; color: #fff; border: none; border-radius: 5px; cursor: pointer; margin-top: 20px; width: 100%; font-size: 16px; }
button:hover { background: #c2185b; }
hr { margin: 15px 0; }
</style>
</head>
<body>

<div class="container">
<h2>Order Summary</h2>
<p><strong>Product:</strong> <?= htmlspecialchars($order['product_name']) ?></p>
<p><strong>Unit Price:</strong> ₹<?= number_format($unit_price, 2) ?></p>
<p><strong>Quantity:</strong> <?= $quantity ?></p>
<p><strong>Delivery Charge:</strong> ₹<?= number_format($delivery_charge, 2) ?></p>
<p class="total"><strong>Total Price (Unit + Delivery):</strong> ₹<?= number_format($total_price, 2) ?></p>
<hr>
<p><strong>Name:</strong> <?= htmlspecialchars($order['first_name']) ?></p>
<p><strong>Mobile:</strong> <?= htmlspecialchars($order['mobile']) ?></p>
<p><strong>Alternate Mobile:</strong> <?= htmlspecialchars($order['alt_mobile']) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
<p><strong>Address:</strong> <?= htmlspecialchars(($order['house_no'] ?? '') . ', ' . ($order['full_address'] ?? '') . ', ' . ($order['landmark'] ?? '') . ', ' . ($order['state'] ?? '') . ' - ' . ($order['pincode'] ?? '')) ?></p>
<p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>

<form action="payment.php" method="POST">
    <!-- Hidden inputs -->
    <input type="hidden" name="product_id" value="<?= htmlspecialchars($order['product_id']) ?>">
    <input type="hidden" name="product_name" value="<?= htmlspecialchars($order['product_name']) ?>">
    <input type="hidden" name="product_price" value="<?= htmlspecialchars($unit_price) ?>">
    <input type="hidden" name="quantity" value="<?= htmlspecialchars($quantity) ?>">
    <input type="hidden" name="delivery_charge" value="<?= htmlspecialchars($delivery_charge) ?>">
    <input type="hidden" name="total_price" value="<?= htmlspecialchars($total_price) ?>">
    <input type="hidden" name="first_name" value="<?= htmlspecialchars($order['first_name']) ?>">
    <input type="hidden" name="mobile" value="<?= htmlspecialchars($order['mobile']) ?>">
    <input type="hidden" name="alt_mobile" value="<?= htmlspecialchars($order['alt_mobile']) ?>">
    <input type="hidden" name="email" value="<?= htmlspecialchars($order['email']) ?>">
    <input type="hidden" name="house_no" value="<?= htmlspecialchars($order['house_no']) ?>">
    <input type="hidden" name="full_address" value="<?= htmlspecialchars($order['full_address']) ?>">
    <input type="hidden" name="landmark" value="<?= htmlspecialchars($order['landmark']) ?>">
    <input type="hidden" name="state" value="<?= htmlspecialchars($order['state']) ?>">
    <input type="hidden" name="pincode" value="<?= htmlspecialchars($order['pincode']) ?>">
    <input type="hidden" name="payment_method" value="<?= htmlspecialchars($order['payment_method']) ?>">

    <button type="submit">Confirm & Pay</button>
</form>
</div>

</body>
</html>
