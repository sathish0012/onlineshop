<?php
session_start();
include "../db.php";

// Only admin can access
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch all orders with product info
$sql = "SELECT o.*, p.name AS product_name, p.image AS product_image, p.price AS product_price, u.name
        FROM orders o
        JOIN products p ON o.product_id = p.id
        JOIN users u ON o.user_id = u.id
        ORDER BY o.order_date DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Orders - Admin</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #fce4ec;
    margin: 0;
    padding: 0;
    color: #333;
}

.dashbord_slider {
    width: 220px;
    height: 100vh;
    background: #fff;
    position: fixed;
    top: 0;
    left: 0;
    padding-top: 20px;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
}

.dashbord_slider ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.dashbord_slider li {
    margin-bottom: 10px;
}

.dashbord_slider a {
    display: block;
    padding: 12px 15px;
    text-decoration: none;
    color: #333;
    border-radius: 5px;
    transition: 0.3s;
}

.dashbord_slider a:hover,
.dashbord_slider a.active {
    background: #e6c9d2;
    color: #fff;
}

.container {
    margin-left: 220px;
    padding: 20px;
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #c2185b;
}

/* Table Styling */
table {
    width: 150%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    table-layout: fixed; /* Ensures column widths are uniform */
}

th, td {
    padding: 12px 10px;
    border: 1px solid #eee;
    font-size: 13px;
    vertical-align: middle; /* Aligns text and images vertically */
    word-wrap: break-word;
}

th {
    background: #ad1457;
    color: #fff;
    text-align: center;
}

tr:hover {
    background: #f9e0ec;
}

.status {
    padding: 4px 8px;
    border-radius: 4px;
    color: #fff;
    font-weight: bold;
    font-size: 12px;
    display: inline-block;
}

.status.Pending { background: #ff9800; }
.status.Success { background: #4caf50; }
.status.Cancelled { background: #f44336; }

.product-img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
    display: block;
    margin: 0 auto; /* Centers the image */
}
.full{
  width: 200px;
}

/* Responsive */
@media(max-width: 1024px){
    .container { margin-left: 220px; padding: 15px; }
    table, th, td { font-size: 12px; }
    .product-img { width: 50px; height: 50px; }
}

@media(max-width: 768px){
    .dashbord_slider { width: 100%; height: auto; position: relative; box-shadow: none; }
    .container { margin-left: 0; padding: 10px; }
    table, th, td { font-size: 11px; }
    .product-img { width: 40px; height: 40px; }
}

/* Specific column alignment */
td:nth-child(1), th:nth-child(1), /* Order ID */
td:nth-child(2), th:nth-child(2), /* User */
td:nth-child(3), th:nth-child(3), /* Product Name */
td:nth-child(5), th:nth-child(5), /* Price */
td:nth-child(14), th:nth-child(14) /* Status */ {
    text-align: center;
}

td:nth-child(4), th:nth-child(4) { text-align: center; } /* Image column */
td:nth-child(6), td:nth-child(7), td:nth-child(8), td:nth-child(9),
td:nth-child(10), td:nth-child(11), td:nth-child(12), td:nth-child(13) {
    text-align: left; /* Align user address/details to left */
}

</style>
</head>
<body>

<div class="dashbord_slider">
<ul>
    <li><a href="dashboard.php">Dashboard</a></li>
    <li><a href="addproduct.php">Add Product</a></li>
    <li><a href="display.php">Update Products</a></li>
    <li><a class="active" href="vieworders.php">View Orders</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>
</div>

<div class="container">
<h2>All Orders</h2>

<?php if(mysqli_num_rows($result) > 0): ?>
<table>
<thead>
<tr>
    <th>Order ID</th>
    <th>User</th>
    <th>Product</th>
    <th>Image</th>
    <th>Price</th>
    <th>First Name</th>
    <th>Mobile</th>
    <th>Alt Mobile</th>
    <th>Email</th>
    <th class="full" >Full Address</th>
    <th>Payment Method</th>
    <th>Status</th>
    <th>Order Date</th>
    <th>Cancel Date</th>
</tr>

</thead>
<tbody>
<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?= htmlspecialchars($row['order_id']) ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['product_name']) ?></td>
    <td><img src="../image/<?= htmlspecialchars($row['product_image']) ?>" class="product-img" alt="<?= htmlspecialchars($row['product_name']) ?>"></td>
    <td>₹<?= number_format($row['product_price']) ?></td>
    <td><?= htmlspecialchars($row['first_name']) ?></td>
    <td><?= htmlspecialchars($row['mobile']) ?></td>
    <td><?= htmlspecialchars($row['alt_mobile']) ?></td>
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td><?= htmlspecialchars($row['house_no'] . ',' . $row['full_address'] . ', ' . $row['landmark'] . ', ' . $row['state'].',' ) ?></td>
    <td><?= htmlspecialchars($row['payment_method']) ?></td>
    <td><span class="status <?= htmlspecialchars($row['payment_status']) ?>"><?= htmlspecialchars($row['payment_status']) ?></span></td>
    <td><?= date('d-m-Y h:i A', strtotime($row['order_date'])) ?></td>
    <td><?= !empty($row['cancel_date']) ? date('d-m-Y h:i A', strtotime($row['cancel_date'])) : '-' ?></td>
</tr>

<?php endwhile; ?>
</tbody>
</table>
<?php else: ?>
<p style="text-align:center;">No orders found.</p>
<?php endif; ?>

</div>
</body>
</html>
