<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch products with stock > 0
$sql = "SELECT * FROM products WHERE stock > 0";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Shop Products</title>
<style>
body { font-family: Arial, sans-serif; background: #fce4ec; margin: 0; padding: 0; color: #333; }
h2 { text-align: center; margin: 30px 0; color: #c2185b; }

.container { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; max-width: 1200px; margin: 0 auto; padding: 20px; }

.product-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    width: 500px;
    text-align: center;
    padding: 15px;
    transition: transform 0.2s;
}

.product-card:hover { transform: translateY(-5px); }

.product-card img {
    width: 100%;
    height: 500px;
    object-fit: cover;
    border-radius: 8px;
}

.product-card h3 { margin: 10px 0 5px 0; color: #ad1457; }
.product-card p { margin: 5px 0; font-weight: bold; color: #6a1b9a; }

button { padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; margin-top: 10px; background: #f06292; color: #fff; transition: background 0.3s; }
button:hover { background: #c2185b; }

form { display: flex; flex-direction: column; margin-top: 10px; }
form input, form select, form textarea { margin: 5px 0; padding: 8px; border-radius: 5px; border: 1px solid #ccc; }
</style>
</head>
<body>

<h2>Our Products</h2>
<div class="container">

<?php while($row = mysqli_fetch_assoc($result)): ?>
    <div class="product-card">
        <img src="image/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
        <h3><?= htmlspecialchars($row['name']) ?></h3>
        <p>Price: ₹<?= number_format($row['price']) ?></p>

        <form action="payment_gateway.php" method="POST">
            <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
            <input type="hidden" name="product_price" value="<?= $row['price'] ?>">

            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="tel" name="mobile" pattern="[0-9]{10}" placeholder="Mobile Number" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="tel" name="alt_mobile" pattern="[0-9]{10}" placeholder="Alternate Mobile">
            <input type="text" name="state" placeholder="State" required>
            <input type="text" name="house_no" placeholder="House No / Street No" required>
            <textarea name="full_address" rows="2" placeholder="Full Address" required></textarea>
            <input type="text" name="landmark" placeholder="Landmark">
            <select name="payment_method" required>
        <option value="">--Select Payment--</option>
        <option value="cashon">Cash on Delivery</option>
        <option value="gpay">GPay / UPI</option>
        <option value="online">Online Payment</option>
    </select>
            <button type="submit">Continu to Payment</button>
        </form>
    </div>
<?php endwhile; ?>

</div>

</body>
</html>
