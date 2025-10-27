<?php
session_start();
include "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's orders
$sql = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY order_date DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Orders</title>
<style>
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

.container {
    margin-left:230px;
    flex:1;
    padding:40px;
}
h2 {
    text-align:center;
    color:#7b1fa2;
    margin-bottom:25px;
    font-size:26px;
    letter-spacing:1px;
}

.table-wrapper {
    overflow-x: auto;
}

table {
    width:100%;
    border-collapse:collapse;
    background:#fff;
    border-radius:10px;
    overflow:hidden;
    box-shadow:0 3px 10px rgba(0,0,0,0.08);
}
th, td {
    padding:14px;
    border-bottom:1px solid #eee;
    text-align:center;
    font-size:15px;
    white-space: nowrap;
}
th {
    background-color:#ab47bc;
    color:white;
    font-weight:600;
}
tr:hover { background-color:#f9e0ec; }

/* Product name column fixed width */
td:nth-child(2), th:nth-child(2) {
    width: 200px;
    text-align: left;
}

/* Payment Status */
.status {
    padding:6px 10px;
    color:#fff;
    font-weight:bold;
    display:inline-block;
    font-size:14px;
}
.status.Success {color:#4caf50; }
.status.Pending { color:#ff9800; }
.status.Failed, .status.Cancelled { color:#f44336; }

/* ORDER STATUS — Enhanced Style */
.order-status {
    padding:8px 14px;
    font-weight:600;
    display:inline-block;
    font-size:13px;
    text-transform: capitalize;
    
}
.order-status.Processing { color: #03a9f4; }
.order-status.Shipped { color: #673ab7; }
.order-status.Delivered { color:#388e3c; }
.order-status.Cancelled { color: #d32f2f; }
.order-status.Pending  {color: #0e5175ff; }

button.cancel-btn {
  padding:5px 10px;
  background:#f44336;
  color:#fff;
  border:none;
  border-radius:5px;
  cursor:pointer;
}
button.cancel-btn:hover { background:#d32f2f; }

@media(max-width:768px){
    .toggle-btn { display:block; }
    .sidebar {
        transform: translateX(-250px);
        z-index: 1000;
    }
    .sidebar.show { transform: translateX(0); }
    .container{ margin-left:0; padding:20px; }
    table, th, td { font-size:13px; }
    th, td { padding:10px; }
}
</style>

</head>
<body>

<button class="toggle-btn" onclick="toggleSidebar()">☰</button>

<div class="sidebar" id="sidebar">
    <ul>
        <li><a href="index.php">Shop</a></li>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a class="active" href="myorders.php">My Orders</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<div class="container">
<h2>🛍️ My Orders</h2>

<?php if(isset($_SESSION['msg'])): ?>
    <p style="text-align:center; color:green; font-weight:bold;">
        <?= $_SESSION['msg']; unset($_SESSION['msg']); ?>
    </p>
<?php endif; ?>

<?php if(mysqli_num_rows($result) > 0): ?>
<div class="table-wrapper">
<table>
<tr>
    <th>Order ID</th>
    <th style="width: 200px;" >Product Name</th>
    <th>Price</th>
    <th>Payment Method</th>
    <th>Payment</th>
    <th>Order Status</th>
    <th>Order Date</th>
    <th>Action</th>
</tr>
<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?= htmlspecialchars($row['order_id']) ?></td>
    <td><?= htmlspecialchars($row['product_name']) ?></td>
    <td>₹<?= htmlspecialchars($row['product_price']) ?></td>
    <td><?= htmlspecialchars($row['payment_method']) ?></td>
    <td><span class="status <?= htmlspecialchars($row['payment_status']) ?>"><?= htmlspecialchars($row['payment_status']) ?></span></td>
    <td><span class="order-status <?= htmlspecialchars($row['order_status']) ?>"><?= htmlspecialchars($row['order_status']) ?></span></td>
    <td><?= date('d-m-Y h:i A', strtotime($row['order_date'])) ?></td>
    <td>
        <?php if($row['payment_status'] == 'Success' && $row['order_status'] != 'Cancelled'): ?>
        <form method="POST" action="cancelorder.php" onsubmit="return confirm('Are you sure you want to cancel this order?');">
            <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
            <button type="submit" class="cancel-btn">Cancel</button>
        </form>
        <?php else: ?>
            <span style="color:red;">Cancelled</span>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
</div>
<?php else: ?>
    <p style="text-align:center;">You have no orders yet.</p>
<?php endif; ?>

<div style="text-align:center; margin-top:20px;">
    <a href="index.php" class="button">⬅ Back to Home</a>
</div>

</div>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('show');
}
</script>

</body>
</html>
