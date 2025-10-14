<?php
session_start();
include "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order = $_SESSION['order'] ?? null;

    if ($order) {
        $user_id = $order['user_id'];
        $product_id = $order['product_id'];
        $product_name = $order['product_name'];
        $product_price = $order['product_price'];
        $first_name = $order['first_name'];
        $mobile = $order['mobile'];
        $alt_mobile = $order['alt_mobile'];
        $email = $order['email'];
        $state = $order['state'];
        $house_no = $order['house_no'];
        $full_address = $order['full_address'];
        $landmark = $order['landmark'];
        $payment_method = $order['payment_method'];
        $payment_status = 'Success';
        $order_date = date('Y-m-d H:i:s');

        // Generate a unique Order ID (e.g., ORD20251014001)
        $unique_id = 'ORD' . date('YmdHis') . rand(100, 999);

        $sql = "INSERT INTO orders (order_id, user_id, product_id, product_name, product_price, first_name, mobile, alt_mobile, email, state, house_no, full_address, landmark, payment_method, payment_status, order_date)
                VALUES ('$unique_id', '$user_id', '$product_id', '$product_name', '$product_price', '$first_name', '$mobile', '$alt_mobile', '$email', '$state', '$house_no', '$full_address', '$landmark', '$payment_method', '$payment_status', '$order_date')";

        if (mysqli_query($conn, $sql)) {
            // reduce stock
            mysqli_query($conn, "UPDATE products SET stock = stock - 1 WHERE id = '$product_id'");

            // remove session order and store order ID to show success
            unset($_SESSION['order']);
            $_SESSION['last_order_id'] = $unique_id;
        } else {
            die("Database Error: " . mysqli_error($conn));
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Successful</title>
<style>
body { font-family: Arial; background: #f3e5f5; text-align: center; padding: 50px; }
.container { background: #fff; padding: 30px; max-width: 400px; margin: auto; border-radius: 8px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
h2 { color: #7b1fa2; }
p { font-size: 16px; color: #333; margin: 8px 0; }
a { text-decoration: none; color: #fff; background: #ab47bc; padding: 10px 20px; border-radius: 5px; display: inline-block; margin-top: 20px; }
a:hover { background: #7b1fa2; }
</style>
</head>
<body>
<div class="container">
    <h2>✅ Payment Successful!</h2>
    <p>Your order has been successfully placed.</p>
    <p><strong>Order ID:</strong> <?= htmlspecialchars($_SESSION['last_order_id'] ?? 'N/A') ?></p>
    <p>Thank you for your purchase!</p>
    <a href="index.php">Back to Home</a>
</div>
</body>
</html>
