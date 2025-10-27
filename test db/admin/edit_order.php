<?php
session_start();
include "../db.php";

// Check admin login
if(!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin'){
    header("Location: ../index.php");
    exit();
}

// Get order ID
if(!isset($_GET['order_id'])){
    header("Location: vieworders.php");
    exit();
}

$order_id = $_GET['order_id'];

// Fetch order details
$sql = "SELECT o.*, p.name AS product_name, p.image AS product_image, p.price AS product_price, 
               u.name AS user_name, u.email, u.phone
        FROM orders o
        JOIN products p ON o.product_id = p.id
        JOIN users u ON o.user_id = u.id
        WHERE o.order_id='$order_id'";

$result = mysqli_query($conn, $sql);
if(!$result || mysqli_num_rows($result) == 0){
    die("Order not found.");
}

$order = mysqli_fetch_assoc($result);

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $order_status = mysqli_real_escape_string($conn, $_POST['order_status']);
    $payment_status = mysqli_real_escape_string($conn, $_POST['payment_status']);
    $house_no = mysqli_real_escape_string($conn, $_POST['house_no']);
    $full_address = mysqli_real_escape_string($conn, $_POST['full_address']);
    $landmark = mysqli_real_escape_string($conn, $_POST['landmark']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $pin_code = mysqli_real_escape_string($conn, $_POST['pincode']); // <-- correct variable

    $update_sql = "UPDATE orders SET 
                    order_status='$order_status', 
                    payment_status='$payment_status', 
                    house_no='$house_no', 
                    full_address='$full_address', 
                    landmark='$landmark', 
                    state='$state',
                    pincode='$pin_code'   -- <-- use correct variable
                   WHERE order_id='$order_id'";

    if(mysqli_query($conn, $update_sql)){
        header("Location: vieworders.php?msg=updated");
        exit();
    } else {
        $error = "Failed to update order: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Order - Admin</title>
<style>
body { font-family: Arial, sans-serif; background: #e8f0fe; margin: 0; padding: 20px; color: #1c1c1c; }
.container { max-width: 700px; margin: 40px auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);}
h2 { text-align:center; color:#1976d2; margin-bottom:20px; }
label { display:block; margin-top:15px; font-weight:500; }
input, select, textarea {
    width: 100%;
    padding: 8px 12px;
    margin-top:5px;
    border-radius: 6px;
    border:1px solid #ccc;
    font-size:14px;
}
textarea { resize: vertical; }
button {
    margin-top: 20px;
    padding: 10px 18px;
    background: #1976d2;
    color:#fff;
    border:none;
    border-radius:6px;
    cursor:pointer;
    font-size:15px;
    transition: 0.3s;
}
button:hover { background: #1565c0; }
.product-img { width:100px; height:100px; object-fit:cover; border-radius:6px; display:block; margin-top:10px; }
.error { color: red; margin-top: 10px; }
</style>
</head>
<body>

<div class="container">
<h2>Edit Order #<?= htmlspecialchars($order['order_id']) ?></h2>

<?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

<form method="POST">
    <label>User Name</label>
    <input type="text" value="<?= htmlspecialchars($order['user_name']) ?>" disabled>

    <label>Email</label>
    <input type="text" value="<?= htmlspecialchars($order['email']) ?>" disabled>

    <label>Mobile</label>
    <input type="text" value="<?= htmlspecialchars($order['phone']) ?>" disabled>

    <label>Product</label>
    <input type="text" value="<?= htmlspecialchars($order['product_name']) ?>" disabled>
    <img src="../image/<?= htmlspecialchars($order['product_image']) ?>" class="product-img">

    <label>Price</label>
    <input type="text" value="₹<?= number_format($order['product_price']) ?>" disabled>

    <!-- Editable Address Fields -->
    <label>House No</label>
    <input type="text" name="house_no" value="<?= htmlspecialchars($order['house_no']) ?>" required>

    <label>Full Address</label>
    <textarea name="full_address" rows="2" required><?= htmlspecialchars($order['full_address']) ?></textarea>

    <label>Landmark</label>
    <input type="text" name="landmark" value="<?= htmlspecialchars($order['landmark']) ?>">

    <label>State</label>
    <input type="text" name="state" value="<?= htmlspecialchars($order['state']) ?>" required>
     
    <label>Pin Code</label>
    <input type="text" name="pincode" value="<?= htmlspecialchars($order['pincode']) ?>" required>

    <label>Order Status</label>
    <select name="order_status" required>
        <option value="Pending" <?= $order['order_status']=='Pending'?'selected':'' ?>>Pending</option>
        <option value="Processing" <?= $order['order_status']=='Processing'?'selected':'' ?>>Processing</option>
        <option value="Delivered" <?= $order['order_status']=='Delivered'?'selected':'' ?>>Delivered</option>
        <option value="Cancelled" <?= $order['order_status']=='Cancelled'?'selected':'' ?>>Cancelled</option>
    </select>

    <label>Payment Status</label>
    <select name="payment_status" required>
        <option value="Pending" <?= $order['payment_status']=='Pending'?'selected':'' ?>>Pending</option>
        <option value="Success" <?= $order['payment_status']=='Success'?'selected':'' ?>>Success</option>
        <option value="Failed" <?= $order['payment_status']=='Failed'?'selected':'' ?>>Failed</option>
    </select>

    <button type="submit">Update Order</button>
</form>

</div>
</body>
</html>
