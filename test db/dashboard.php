<?php
session_start();
include "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['user_role'] !== 'user') {
    header("Location: admin/dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$user_res = mysqli_query($conn, "SELECT name FROM users WHERE id='$user_id' LIMIT 1");
$user_name = mysqli_num_rows($user_res) > 0 ? mysqli_fetch_assoc($user_res)['name'] : 'User';

// Orders summary
$total_orders_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders WHERE user_id='$user_id'");
$total_orders = mysqli_fetch_assoc($total_orders_res)['total'];

$pending_orders_res = mysqli_query($conn, "SELECT COUNT(*) AS pending FROM orders WHERE user_id='$user_id' AND payment_status='Pending'");
$pending_orders = mysqli_fetch_assoc($pending_orders_res)['pending'];

$cancelled_orders_res = mysqli_query($conn, "SELECT COUNT(*) AS cancelled FROM orders WHERE user_id='$user_id' AND payment_status='Cancelled'");
$cancelled_orders = mysqli_fetch_assoc($cancelled_orders_res)['cancelled'];

// Total spent on successful orders
$total_spent_res = mysqli_query($conn, "SELECT SUM(product_price) AS total_spent FROM orders WHERE user_id='$user_id' AND payment_status='Success'");
$total_spent = mysqli_fetch_assoc($total_spent_res)['total_spent'] ?? 0;

// Fetch orders for table
$orders_res = mysqli_query($conn, "
    SELECT o.*, p.name AS product_name, p.price AS product_price
    FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE o.user_id='$user_id'
    ORDER BY o.order_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard</title>
<style>
/* RESET & BASE */
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #fce4ec;
    color: #333;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

/* SIDEBAR */
.sidebar {
    width: 220px;
    background: #fff;
    box-shadow: 2px 0 8px rgba(0,0,0,0.15);
    position: fixed;
    left: 0;
    top: 0;
    padding-top: 55px;
    height: 100vh;
    overflow-y: auto;
    transition: transform 0.3s ease;
}
.sidebar ul { list-style: none; padding: 0; }
.sidebar li { margin-bottom: 10px; }
.sidebar a {
    display: block;
    padding: 12px 25px;
    text-decoration: none;
    color: #333;
    font-size: 16px;
    font-weight: 500;
    border-radius: 8px;
    transition: 0.3s;
}
.sidebar a.active, .sidebar a:hover {
    background: #f06292;
    color: #fff;
}

/* TOGGLE BUTTON */
.toggle-btn {
    display: none;
    position: fixed;
    top: 10px;
    left: 10px;
    background: #ab47bc;
    color: #fff;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    z-index: 1001;
    font-size: 18px;
}

/* MAIN CONTENT */
.container {
    margin-left: 220px;
    flex: 1;
    padding: 30px 50px;
    display: flex;
    flex-direction: column;
    gap: 25px;
}
h2.welcome {
    text-align: center;
    color: #7b1fa2;
    font-weight: bold;
    font-size: 2rem;
    margin-bottom: 25px;
}

/* SUMMARY CARDS */
.summary-cards {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    justify-content: center;
    padding-right: 30px;
}
.card {
    flex: 1;
    min-width: 190px;
    max-width: 290px;
    padding: 25px 20px;
    border-radius: 12px;
    background: linear-gradient(145deg, #fff, #fce4ec);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    border-left: 5px solid #f06292;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.15);
}
.card h3 { margin: 10px 0 0; font-size: 1.6rem; color: #c2185b; }
.card p { font-size: 0.95rem; color: #555; }
.card.revenue { border-left-color: #ff9800; }
.card.cancelled { border-left-color: #f44336; }

/* ORDERS TABLE */
.orders-table-container { overflow-x: auto; width: 100%; }
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}
th, td {
    padding: 14px;
    border-bottom: 1px solid #eee;
    text-align: center;
    font-size: 14px;
}
th { background-color: #ab47bc; color: white; font-weight: 600; }
tr:hover { background-color: #f9e0ec; }
.status { padding: 6px 12px; font-weight: bold; display: inline-block; font-size: 13px; }
.status.Success { color: #4caf50; }
.status.Pending { color: #ff9800; }
.status.Cancelled { color: #f44336; }

/* Force date to stay in single line */
th.date, td.date { white-space: nowrap; }

/* BUTTON */
a.button {
    background: #ab47bc;
    color: #fff;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 25px;
    display: inline-block;
    margin-top: 20px;
    font-weight: bold;
    transition: 0.3s ease;
}
a.button:hover { background: #7b1fa2; }

/* RESPONSIVE */
@media(max-width:1024px){
    .container { margin-left: 200px; padding: 20px; }
    .sidebar { width: 200px; }
}
@media(max-width:768px){
    .toggle-btn { display: block; }
    .sidebar { width: 200px; transform: translateX(-100%); position: fixed; z-index: 1000; }
    .sidebar.show { transform: translateX(0); }
    .container { margin-left: 0; padding: 15px; }
    .summary-cards { flex-direction: column; align-items: center; }
    table, th, td { font-size: 12px; }
}
@media(max-width:480px){
    .card { width: 100%; max-width: 100%; }
    th, td { padding: 8px; }
}

/* WHATSAPP BUTTON */
#whatsappBtn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #25d366;
    border-radius: 50%;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    transition: transform 0.3s, box-shadow 0.3s;
}
#whatsappBtn img { width: 35px; height: 35px; }
#whatsappBtn:hover { transform: scale(1.1); box-shadow: 0 6px 15px rgba(0,0,0,0.4); }
</style>
</head>
<body>

<button class="toggle-btn" onclick="toggleSidebar()">☰</button>

<div class="sidebar" id="sidebar">
    <ul>
        <li><a href="index.php">Shop</a></li>
        <li><a class="active" href="dashboard.php">Dashboard</a></li>
        <li><a href="myorders.php">My Orders</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<div class="container">
<h2 class="welcome">Welcome, <?= htmlspecialchars($user_name) ?>!</h2>

<div class="summary-cards">
    <div class="card orders">
        <p>Total Orders</p>
        <h3><?= $total_orders ?></h3>
    </div>
    <div class="card revenue">
        <p>Pending Orders</p>
        <h3><?= $pending_orders ?></h3>
    </div>
    <div class="card cancelled">
        <p>Cancelled Orders</p>
        <h3><?= $cancelled_orders ?></h3>
    </div>
    <div class="card revenue">
        <p>Total Spent</p>
        <h3>₹<?= number_format($total_spent, 2) ?></h3>
    </div>
</div>

<h3 style="text-align:center; margin-bottom:15px;">🛒 My Orders</h3>

<div class="orders-table-container">
<?php if(mysqli_num_rows($orders_res) > 0): ?>
<table>
<tr>
    <th>Order ID</th>
    <th>Product</th>
    <th>Price</th>
    <th>Payment Method</th>
    <th>Payment</th>
    <th class="date">Order Date</th>
</tr>
<?php while($order = mysqli_fetch_assoc($orders_res)): ?>
<tr>
    <td><?= htmlspecialchars($order['order_id']) ?></td>
    <td><?= htmlspecialchars($order['product_name']) ?></td>
    <td>₹<?= htmlspecialchars($order['product_price']) ?></td>
    <td><?= htmlspecialchars($order['payment_method']) ?></td>
    <td><span class="status <?= htmlspecialchars($order['payment_status']) ?>"><?= htmlspecialchars($order['payment_status']) ?></span></td>
    <td class="date"><?= date('d-m-Y h:i A', strtotime($order['order_date'])) ?></td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<p style="text-align:center;">You have no orders yet.</p>
<?php endif; ?>
</div>

<div style="text-align:center;">
    <a href="index.php" class="button">⬅ Back to Home</a>
</div>
</div>

<!-- WhatsApp Floating Button -->
<a href="https://wa.me/919363587844" target="_blank" id="whatsappBtn" title="Chat with us on WhatsApp">
    <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" />
</a>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('show');
}
</script>

</body>
</html>
