<?php
session_start();
$order = $_SESSION['order'] ?? null;

if (!$order) {
    echo "No order found.";
    exit;
}
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
button { padding: 12px 20px; background: #f06292; color: #fff; border: none; border-radius: 5px; cursor: pointer; margin-top: 20px; width: 100%; font-size: 16px; }
button:hover { background: #c2185b; }
</style>
</head>
<body>

<div class="container">
<h2>Order Summary</h2>
<p><strong>Product:</strong> <?= htmlspecialchars($order['product_name']) ?></p>
<p><strong>Price:</strong> ₹<?= number_format($order['product_price'], 2) ?></p>
<p><strong>Name:</strong> <?= htmlspecialchars($order['first_name']) ?></p>
<p><strong>Mobile:</strong> <?= htmlspecialchars($order['mobile']) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
<p><strong>Address:</strong> <?= htmlspecialchars($order['house_no'] . ', ' . $order['full_address'] . ', ' . $order['landmark'] . ', ' . $order['state']) ?></p>
<p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>

<form action="payment.php" method="POST">
    <button type="submit">Confirm & Pay</button>
</form>
</div>

</body>
</html>
