<?php
// ----------------------
// ENABLE ERROR DISPLAY
// ----------------------
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "db.php";

// ----------------------
// CHECK DATABASE CONNECTION
// ----------------------
if (!$conn) {
    die("❌ Database connection failed: " . mysqli_connect_error());
}

// ----------------------
// CHECK LOGIN STATUS
// ----------------------
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ----------------------
// PROCESS ORDER
// ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $order = $_SESSION['order'] ?? null;

    if (!$order) {
        die("⚠️ No order data found in session.");
    }

    $user_id = $_SESSION['user_id'];
    $product_id = $order['product_id'];
    $product_name = mysqli_real_escape_string($conn, $order['product_name']);
    $product_price = $order['product_price']; // main price per item
    $quantity = $order['quantity'] ?? 1; // quantity is stored but not multiplied for total
    $delivery_charge = 50; // fixed delivery charge
    $total_price = $product_price + $delivery_charge; // ✅ Only main price + delivery charge

    $first_name = mysqli_real_escape_string($conn, $order['first_name']);
    $mobile = mysqli_real_escape_string($conn, $order['mobile']);
    $alt_mobile = mysqli_real_escape_string($conn, $order['alt_mobile']);
    $email = mysqli_real_escape_string($conn, $order['email']);
    $state = mysqli_real_escape_string($conn, $order['state']);
    $house_no = mysqli_real_escape_string($conn, $order['house_no']);
    $full_address = mysqli_real_escape_string($conn, $order['full_address']);
    $landmark = mysqli_real_escape_string($conn, $order['landmark']);
    $pincode = mysqli_real_escape_string($conn, $order['pincode']);
    $payment_method = mysqli_real_escape_string($conn, $order['payment_method']);
    $payment_status = 'Success';
    $order_date = date('Y-m-d H:i:s');

    // ----------------------
    // GENERATE UNIQUE ORDER ID
    // ----------------------
    $unique_id = 'ORD' . date('YmdHis') . rand(100, 999);

    // ----------------------
    // INSERT ORDER RECORD WITH TOTAL PRICE (MAIN PRICE + DELIVERY)
    // ----------------------
    $sql = "INSERT INTO orders 
        (order_id, user_id, product_id, product_name, product_price, quantity, total_price, first_name, mobile, alt_mobile, email, state, house_no, full_address, landmark, pincode, payment_method, payment_status, order_date)
        VALUES 
        ('$unique_id', '$user_id', '$product_id', '$product_name', '$product_price', '$quantity', '$total_price', '$first_name', '$mobile', '$alt_mobile', '$email', '$state', '$house_no', '$full_address', '$landmark', '$pincode', '$payment_method', '$payment_status', '$order_date')";

    if (mysqli_query($conn, $sql)) {

        // ----------------------
        // REDUCE STOCK IN PRODUCTS
        // ----------------------
        $update_stock = mysqli_query($conn, "UPDATE products SET stock = stock - $quantity WHERE id = '$product_id'");
        if (!$update_stock) {
            die("❌ Failed to update stock: " . mysqli_error($conn));
        }

        unset($_SESSION['order']);
        $_SESSION['last_order_id'] = $unique_id;

    } else {
        die("❌ Database Error: " . mysqli_error($conn) . "<br>Query: " . $sql);
    }
}

// ----------------------
// SHOW SUCCESS PAGE
// ----------------------
$order_id = $_SESSION['last_order_id'] ?? 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Successful</title>
<style>
body { font-family: Arial, sans-serif; background: #f3e5f5; text-align: center; padding: 50px; }
.container { background: #fff; padding: 30px; max-width: 450px; margin: auto; border-radius: 8px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
h2 { color: #7b1fa2; margin-bottom: 15px; }
p { font-size: 16px; color: #333; margin: 8px 0; }
a { text-decoration: none; color: #fff; background: #ab47bc; padding: 10px 20px; border-radius: 5px; display: inline-block; margin-top: 20px; }
a:hover { background: #7b1fa2; }
</style>
</head>
<body>
<div class="container">
    <h2>✅ Payment Successful!</h2>
    <p>Your order has been successfully placed.</p>
    <p><strong>Order ID:</strong> <?= htmlspecialchars($order_id) ?></p>
    <p><strong>Quantity:</strong> <?= htmlspecialchars($quantity) ?></p>
    <p><strong>Total Price:</strong> ₹<?= number_format($total_price, 2) ?></p>
    <p><strong>Pincode:</strong> <?= htmlspecialchars($pincode ?? '') ?></p>
    <p><strong>Order Date:</strong> <span id="currentTime"></span></p>
    <p>Thank you for your purchase!</p>
    <a href="index.php">Back to Home</a>
</div>

<script>
function updateTime() {
    const now = new Date();
    const day = String(now.getDate()).padStart(2, '0');
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const year = now.getFullYear();
    let hours = now.getHours();
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12 || 12;
    const timeString = `${day}-${month}-${year} ${hours}:${minutes} ${ampm}`;
    document.getElementById('currentTime').textContent = timeString;
}
updateTime();
setInterval(updateTime, 60000);
</script>
</body>
</html>
