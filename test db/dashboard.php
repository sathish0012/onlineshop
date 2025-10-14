<?php
session_start();
include "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$user_res = mysqli_query($conn, "SELECT name FROM users WHERE id='$user_id' LIMIT 1");
$user_name = mysqli_num_rows($user_res) > 0 ? mysqli_fetch_assoc($user_res)['name'] : 'User';

// Fetch orders stats
$total_orders_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders WHERE user_id='$user_id'");
$total_orders = mysqli_fetch_assoc($total_orders_res)['total'];

$pending_orders_res = mysqli_query($conn, "SELECT COUNT(*) AS pending FROM orders WHERE user_id='$user_id' AND payment_status='Pending'");
$pending_orders = mysqli_fetch_assoc($pending_orders_res)['pending'];

$cancelled_orders_res = mysqli_query($conn, "SELECT COUNT(*) AS cancelled FROM orders WHERE user_id='$user_id' AND payment_status='Cancelled'");
$cancelled_orders = mysqli_fetch_assoc($cancelled_orders_res)['cancelled'];

// Fetch all user's orders
$orders_res = mysqli_query($conn, "SELECT * FROM orders WHERE user_id='$user_id' ORDER BY order_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard</title>
<style>
/* ===== Body & Sidebar ===== */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background: #fce4ec;
    display: flex;
    min-height: 100vh;
    color: #333;
}

.dashbord_sliders {
    width: 230px;
    background: #fff;
    box-shadow: 2px 0 8px rgba(0,0,0,0.1);
    padding-top: 30px;
    position: fixed;
    left: 0;
    top: 0;
    height: 100vh;
}

.dashbord_sliders ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.dashbord_sliders li {
    margin-bottom: 8px;
}

.dashbord_sliders a {
    display: block;
    padding: 12px 25px;
    text-decoration: none;
    color: #333;
    font-size: 16px;
    font-weight: 500;
    border-radius: 8px;
    transition: 0.3s;
}

.dashbord_sliders a.active, 
.dashbord_sliders a:hover {
    background: #e6c9d2;
    color: #fff;
}

/* ===== Main Container ===== */
.container {
    margin-left: 230px;
    flex: 1;
    padding: 30px 40px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

h2 {
   
    color: #7b1fa2;
    margin-bottom: 5px;
}

h3.welcome {
    text-align: center;
    color: #7b1fa2;
    margin-bottom: 25px;
    font-weight: normal;
}

/* ===== Stats Cards ===== */
    /* Summary Cards */
    .summary-cards {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }
    .card {
        flex: 1;
        min-width: 200px;
        padding: 20px;
        border-radius: 8px;
        background-color: white;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-left: 5px solid #f06292;
    }
    .card h3 {
        margin: 0 0 10px 0;
        color: #c2185b;
        font-size: 1rem;
        text-transform: uppercase;
    }
    .card p {
        font-size: 1.5rem;
        font-weight: bold;
        margin: 0;
        color: #333;
    }

    .card.revenue { border-left-color: #28a745; }
    .card.customers { border-left-color: #007bff; }

/* ===== Orders Table ===== */
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #eee;
    text-align: center;
    font-size: 14px;
}

th {
    background-color: #ab47bc;
    color: white;
    font-weight: 600;
}

tr:hover {
    background-color: #f9e0ec;
}

/* ===== Status Badges ===== */
.status {
    padding: 5px 10px;
    border-radius: 5px;
    color: #fff;
    font-weight: bold;
    display: inline-block;
    font-size: 13px;
}

.status.Success { background: #4caf50; }
.status.Pending { background: #ff9800; }
.status.Cancelled { background: #f44336; }

/* ===== Buttons ===== */
a.button {
    background: #ab47bc;
    color: #fff;
    text-decoration: none;
    padding: 8px 15px;
    border-radius: 5px;
    display: inline-block;
    margin-top: 10px;
    transition: 0.3s ease;
}

a.button:hover {
    background: #7b1fa2;
}

/* ===== Responsive ===== */
@media(max-width:768px){
    .dashbord_sliders {
        width: 100%;
        height: auto;
        position: relative;
        box-shadow: none;
    }
    .container {
        margin-left: 0;
        padding: 15px;
    }
    table, th, td {
        font-size: 12px;
    }
   
}

</style>
</head>
<body>

<div class="dashbord_sliders">
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
        <center> <p style="font-size: medium;" >Total Orders</p></center>
        <center> <h3 style="margin-top :15px;" ><?= $total_orders ?></h3></center>
    </div>
    <div class="card revenue">
       <center>  <p style="font-size: medium;">Pending Orders</p></center>
       <center> <h3 style="margin-top :15px;"><?= $pending_orders ?></h3></center>
    </div>
    <div class="card customers">
        <center> <p style="font-size: medium;" >Cancelled Orders</p></center>
       <center>  <h3 style="margin-top :15px;"><?= $cancelled_orders ?></h3></center>
    </div>
</div>

<h3 style="text-align:center; margin-bottom:15px;">🛒 My Orders</h3>

<?php if(isset($_SESSION['msg'])): ?>
<p style="text-align:center; color:green; font-weight:bold;"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></p>
<?php endif; ?>

<?php if(mysqli_num_rows($orders_res) > 0): ?>
<table>
<tr>
    <th>Order ID</th>
    <th>Product</th>
    <th>Price</th>
    <th>Payment Method</th>
    <th>Status</th>
    <th>Order Date</th>
</tr>
<?php while($order = mysqli_fetch_assoc($orders_res)): ?>
<tr>
    <td><?= htmlspecialchars($order['order_id']) ?></td>
    <td><?= htmlspecialchars($order['product_name']) ?></td>
    <td>₹<?= htmlspecialchars($order['product_price']) ?></td>
    <td><?= htmlspecialchars($order['payment_method']) ?></td>
    <td><span class="status <?= htmlspecialchars($order['payment_status']) ?>"><?= htmlspecialchars($order['payment_status']) ?></span></td>
    <td><?= date('d-m-Y h:i A', strtotime($order['order_date'])) ?></td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<p style="text-align:center;">You have no orders yet.</p>
<?php endif; ?>

<div style="text-align:center; margin-top:20px;">
    <a href="index.php" class="button">⬅ Back to Home</a>
</div>

</div>
</body>
</html>
