<?php
session_start();
include "../db.php"; // Database connection

// 1️⃣ Only allow logged-in admin
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// 2️⃣ Summary Data: Total Orders, Total Revenue
$total_orders_query = "
    SELECT COUNT(*) AS total_orders, 
           SUM(p.price ) AS total_revenue
    FROM orders o
    JOIN products p ON o.product_id = p.id
";
$total_orders_result = mysqli_query($conn, $total_orders_query);
$summary = mysqli_fetch_assoc($total_orders_result);

// Total Users
$total_users_query = "SELECT COUNT(*) AS total_users FROM users";
$total_users_result = mysqli_query($conn, $total_users_query);
$total_users = mysqli_fetch_assoc($total_users_result)['total_users'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<style>
body { background: #fce4ec; font-family: Arial,sans-serif; margin:0; padding:0; color:#333; }
.dashbord_slider { width:220px; height:100vh; background:#fff; padding-top:20px; box-shadow:2px 0 5px rgba(0,0,0,0.1); position:fixed; top:0; left:0;}
.dashbord_slider ul { list-style:none; padding:0; margin:0;}
.dashbord_slider li { margin-bottom:10px;}
.dashbord_slider a { display:block; padding:12px 15px; text-decoration:none; color:#333; border-radius:5px; transition:0.3s;}
.dashbord_slider a:hover, .dashbord_slider a.active { background:#f1a8c0; color:white;}

.content { margin-left:240px; padding:20px; }
h2 { color:#c2185b; margin-bottom:20px; text-align:center; }

/* Summary Cards */
.summary-cards { display:flex; gap:20px; flex-wrap:wrap; margin-bottom:30px; justify-content:center;}
.card { flex:1; min-width:180px; padding:20px; border-radius:8px; background:white; box-shadow:0 4px 8px rgba(0,0,0,0.1); border-left:5px solid #f06292; text-align:center;}
.card h3 { margin:0 0 10px 0; color:#c2185b; font-size:1rem; text-transform:uppercase;}
.card p { font-size:1.5rem; font-weight:bold; margin:0; color:#333; }
.card.revenue { border-left-color:#28a745; }
.card.users { border-left-color:#007bff; }



@media(max-width:768px){
  .dashbord_slider { width:100%; height:auto; position:relative; box-shadow:none;}
  .content { margin-left:0; padding:10px;}
  .orders-table, .orders-table th, .orders-table td { font-size:11px; }
  .orders-table img { width:40px; height:40px; }
}
</style>
</head>
<body>

<div class="dashbord_slider">
<ul>
    <li><a class="active" href="dashboard.php">Dashboard</a></li>
    <li><a href="addproduct.php">Add Product</a></li>
    <li><a href="display.php">Update Products</a></li>
    <li><a href="vieworders.php">View Orders</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>
</div>

<div class="content">
<h2>📊 Dashboard Overview</h2>

<div class="summary-cards">
    <div class="card orders">
        <h3>Total Orders</h3>
        <p><?= $summary['total_orders'] ?? 0 ?></p>
    </div>
    <div class="card revenue">
        <h3>Total Revenue</h3>
        <p>₹<?= number_format($summary['total_revenue'] ?? 0, 2) ?></p>
    </div>
    <div class="card users">
        <h3>Total Users</h3>
        <p><?= $total_users ?></p>
    </div>
</div>

</body>
</html>
