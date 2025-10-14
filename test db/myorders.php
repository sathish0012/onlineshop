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
body {
  font-family: Arial, sans-serif;
  background: #fce4ec;
  margin: 0;
  padding: 0;
  color: #333;
  display: flex;
  min-height: 100vh;
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
.dashbord_sliders ul { list-style:none; padding:0; margin:0;}
.dashbord_sliders li { margin-bottom:8px;}
.dashbord_sliders a {
  display:block; padding:12px 25px; text-decoration:none;
  color:#333; font-size:16px; font-weight:500; border-radius:8px; transition:0.3s;
}
.dashbord_sliders a.active,
.dashbord_sliders a:hover { background:#e6c9d2; color:#fff; }

.container { margin-left:230px; flex:1; padding:40px; }
h2 { text-align:center; color:#7b1fa2; margin-bottom:25px; font-size:26px; letter-spacing:1px; }

table { width:100%; border-collapse:collapse; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 3px 10px rgba(0,0,0,0.08);}
th, td { padding:14px; border-bottom:1px solid #eee; text-align:center; font-size:15px; }
th { background-color:#ab47bc; color:white; font-weight:600; }
tr:hover { background-color:#f9e0ec; }

.status { padding:6px 10px; border-radius:5px; color:#fff; font-weight:bold; display:inline-block; font-size:14px; }
.status.Success { background:#4caf50; }
.status.Pending { background:#ff9800; }
.status.Failed { background:#f44336; }
.status.Cancelled { background:#f44336; }

button.cancel-btn {
  padding:5px 10px; background:#f44336; color:#fff; border:none; border-radius:5px; cursor:pointer;
}
button.cancel-btn:hover { background:#d32f2f; }

@media(max-width:768px){
  .dashbord_sliders { width:100%; height:auto; position:relative; box-shadow:none; }
  .container{ margin-left:0; padding:20px; }
  table, th, td { font-size:13px; }
}
</style>
</head>
<body>

<div class="dashbord_sliders">
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
<table>
<tr>
    <th>Order ID</th>
    <th>Product Name</th>
    <th>Price</th>
    <th>Payment Method</th>
    <th>Status</th>
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
    <td><?= date('d-m-Y h:i A', strtotime($row['order_date'])) ?></td>
    <td>
        <?php if($row['payment_status'] == 'Success' || $row['payment_status'] == 'Pending'): ?>
        <form method="POST" action="cancelorder.php" onsubmit="return confirm('Are you sure you want to cancel this order?');">
            <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
            <button type="submit" class="cancel-btn">Cancel</button>
        </form>
        <?php else: ?>
            <span style="color:red;">Cannot cancel</span>
        <?php endif; ?>
    </td>
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
