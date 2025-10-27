<?php
session_start();
include "db.php";

// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if product_id is provided
if (!isset($_GET['product_id'])) {
    echo "<h3 style='color:red; text-align:center;'>No product selected!</h3>";
    exit();
}

$product_id = intval($_GET['product_id']);
$sql = "SELECT * FROM products WHERE id = $product_id LIMIT 1";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "<h3 style='color:red; text-align:center;'>Product not found!</h3>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Buy Product</title>
<style>
body { font-family: Arial, sans-serif; background: #fce4ec; margin:0; padding:0; color:#333; }
.container { max-width:600px; margin:50px auto; padding:20px; background:#fff; border-radius:10px; box-shadow:0 3px 10px rgba(0,0,0,0.1);}
img { width:100%; height:fit-content; object-fit:cover; border-radius:8px; }
input, select { margin:5px 0; padding:10px; border-radius:5px; border:1px solid #ccc; width:100%; }
button { padding:12px; background:#f06292; color:#fff; border:none; border-radius:5px; cursor:pointer; margin-top:10px; width:100%; }
button:hover { background:#c2185b; }
.quantity-container { display:flex; align-items:center; margin:10px 0; }
.quantity-container button { width:40px; height:30px; margin:0; font-weight:bold; background:#f06292; color:#fff; border:none; cursor:pointer; }
.quantity-container input { width:60px; text-align:center; border:none; font-size:16px; margin:0 5px; }
</style>
</head>
<body>

<div class="container">
    <h2><?= htmlspecialchars($product['name']) ?></h2>
    <img src="image/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
    <p>Price per item: ₹<span id="price"><?= number_format($product['price']) ?></span></p>

    <!-- Quantity Selector -->
    <div class="quantity-container">
        <button type="button" onclick="changeQty(-1)">−</button>
        <input type="number" id="quantity" value="1" min="1" max="<?= $product['stock'] ?>" readonly>
        <button type="button" onclick="changeQty(1)">+</button>
    </div>
    <p>Total Price: ₹<span id="total_price"><?= number_format($product['price']) ?></span></p>

    <!-- Checkout Form -->
    <form action="payment_gateway.php" method="POST" onsubmit="return validateForm()">
    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
    <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>"> <!-- Added -->
    <input type="hidden" name="product_price" id="final_price" value="<?= $product['price'] ?>">
    <input type="hidden" name="quantity" id="final_quantity" value="1">

    <input type="text" name="first_name" placeholder="Full Name" required>
    <input type="tel" name="mobile" placeholder="Mobile Number" pattern="[0-9]{10}" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="tel" name="alt_mobile" placeholder="Alternate Mobile" pattern="[0-9]{10}">
    <input type="text" name="house_no" placeholder="House No / Street" required>
    <input type="text" name="full_address" placeholder="Full Address" required>
    <input type="text" name="landmark" placeholder="Landmark">
    <input type="text" name="state" placeholder="State" required>
    <input type="text" name="pincode" placeholder="Pincode" pattern="[0-9]{6}" required>

    <select name="payment_method" required>
        <option value="">--Select Payment--</option>
        <option value="cashon">Cash on Delivery</option>
        <option value="gpay">GPay / UPI</option>
        <option value="online">Online Payment</option>
    </select>

    <button type="submit">Continue to Payment</button>
</form>

</div>

<script>
const pricePerItem = <?= $product['price'] ?>;
const maxStock = <?= $product['stock'] ?>;
const qtyInput = document.getElementById('quantity');
const totalDisplay = document.getElementById('total_price');
const finalPrice = document.getElementById('final_price');
const finalQuantity = document.getElementById('final_quantity');

function updateTotal(){
    const qty = parseInt(qtyInput.value);
    const total = pricePerItem * qty;
    totalDisplay.textContent = total.toLocaleString();
    finalPrice.value = total;
    finalQuantity.value = qty;
}

function changeQty(change){
    let qty = parseInt(qtyInput.value) + change;
    if(qty < 1) qty = 1;
    if(qty > maxStock) qty = maxStock;
    qtyInput.value = qty;
    updateTotal();
}

updateTotal();

function validateForm(){
    const form = document.forms[0];
    const requiredFields = ['first_name','mobile','email','house_no','full_address','state','pincode'];
    for(const field of requiredFields){
        if(form[field].value.trim() === ''){
            alert('Please fill all required fields!');
            form[field].focus();
            return false;
        }
    }
    return true;
}
</script>

</body>
</html>
