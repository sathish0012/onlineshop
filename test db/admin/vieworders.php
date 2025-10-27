<?php
session_start();
include "../db.php";

if(!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin'){
    header("Location: ../index.php");
    exit();
}

// Fetch all orders
$sql = "SELECT o.*, p.name AS product_name, p.image AS product_image, o.total_price AS product_price, u.name
        FROM orders o
        JOIN products p ON o.product_id = p.id
        JOIN users u ON o.user_id = u.id
        ORDER BY o.order_date DESC";


$result = mysqli_query($conn, $sql);
if(!$result){
    die("Error fetching orders: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Orders - Admin</title>
<style>
/* Your CSS here (unchanged) */
body {
    font-family: Arial, sans-serif;
    background: #e8f0fe;
    margin: 0;
    color: #1c1c1c;
}

/* Sidebar */
.sidebar {
    width: 220px;
    height: 100vh;
    background: #1a237e;
    position: fixed;
    top: 0;
    left: 0;
    padding-top: 60px;
    box-shadow: 2px 0 8px rgba(0,0,0,0.15);
    transition: transform 0.3s ease;
    z-index: 1000;
}
.sidebar ul { list-style: none; padding:0; margin:0; }
.sidebar li { margin-bottom:10px; }
.sidebar a {
    display:block;
    padding:12px 25px;
    text-decoration:none;
    color:#e8eaf6;
    border-radius:8px;
    transition:0.3s;
}
.sidebar a:hover, .sidebar a.active { background:#3949ab; color:#fff; }

/* Toggle Button */
.toggle-btn {
    display:none;
    position:fixed;
    top:15px;
    left:15px;
    background:#3949ab;
    color:#fff;
    border:none;
    padding:10px 15px;
    border-radius:5px;
    cursor:pointer;
    z-index:1001;
    font-size:18px;
    box-shadow:0 3px 6px rgba(0,0,0,0.2);
}

/* Container */
.container {
    margin-left: 240px;
    padding: 20px;
}

/* Heading */
h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #c2185b;
}

/* Table for Desktop */
.table-wrapper {
    overflow-x:auto;
}
table {
    width: 100%;
    min-width: 900px;
    border-collapse: collapse;
    background:#fff;
    border-radius:8px;
    overflow:hidden;
    box-shadow:0 3px 10px rgba(0,0,0,0.1);
    table-layout: fixed;
}
th, td {
    padding:10px;
    border:1px solid #eee;
    font-size:13px;
    word-wrap:break-word;
}
th {
    background:#3949ab;
    color:#fff;
    text-align:center;
}
tr:hover { background:#bbdefb; }

/* Status Labels */
.status {
    padding:4px 8px;
    border-radius:4px;
    color:#fff;
    font-weight:bold;
    font-size:12px;
    display:inline-block;
}
.status.Pending { background:#ff9800; }
.status.Processing { background:#2196f3; }
.status.Delivered { background:#4caf50; }
.status.Cancelled { background:#f44336; }
.status.Success { background:#4caf50; }

/* Dropdown style */
.status-dropdown {
    width: fit-content;
    padding:5px;
    border-radius:5px;
    border:1px solid #ccc;
    font-size:12px;
}

/* Product Image */
.product-img {
    width:60px;
    height:60px;
    object-fit:cover;
    border-radius:4px;
    display:block;
    margin:0 auto;
}

/* New order highlight */
.new-order {
    background-color: #fff9c4; /* Light yellow */
}
.new-icon {
    width: 40px;
    height: auto;
    margin-left: 5px;
    vertical-align: middle;
    margin-top: -150px;
    animation: pulse 1.2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.15); }
    75% { transform: scale(1.25); }
    100% { transform: scale(1.15); }
}

.toggle-btn {
    display:none;
    position:fixed;
    top:15px;
    left:15px;
    
}
/* Search container */
.search-container {
    position: relative;
    width: 100%;
    text-align: right;
    margin-bottom:10px;
    top: 80px;
    right: 50px;
}

/* Input box */
.search-container input#searchBox {
    width: 200px;
    padding: 6px 10px 6px 30px; /* left padding for icon if needed */
    border-radius: 4px;
    border: 1px solid #ccc;
    font-size: 14px;
}

/* Search icon */
.search-container #searchBtn {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #3949ab;
    font-size: 16px;
}

@media(max-width:768px){
    .new-icon {
        width: 30px;
        height: 30px;
        margin: 0 0 5px 0;
        vertical-align: baseline;
        display: inline-block;
        position: relative;
        top: 20;
        left: 20;margin-left: 20px;
        margin-bottom: -5px;
        margin-top: 0;
    }
}
@media(max-width:768px){
    .search-container {
        text-align: left; /* or center if you like */
        position: static; /* removes top/right offset */
        margin: 10px 0 15px 0;
    }

    .search-container input#searchBox {
        height: 20px;
       margin-top: 15PX;
        margin-left: 20%;
        width: 70%; /* full width for mobile */
        padding: 6px 10px;
    }

    .search-container #searchBtn {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }
}



/* Responsive */
@media(max-width:768px){
    .toggle-btn { display:block; }
    .sidebar { transform:translateX(-100%); width:200px; position: fixed; }
    .sidebar.show { transform:translateX(0); }

    .container {
        margin: 0 auto;
        padding:10px;
        width:500px;
        box-sizing: border-box;
    }

    table thead { display:none; }
    table, tbody, tr, td { display:block; width:100%; }
    tr {
        margin-bottom: 15px;
        border:1px solid #ccc;
        border-radius:10px;
        background:#fff;
        padding:10px;
        box-shadow:0 2px 6px rgba(0,0,0,0.1);
    }
    td {
        padding:5px 10px;
        text-align:left;
        position:relative;
        padding-left:130px;
        margin-bottom:5px;
        overflow:hidden;
        white-space:nowrap;
        text-overflow:ellipsis;
    }
    td img.product-img {
        width: 80px;
        height: 100px;
        margin:0px;
        padding:0px;
        border-radius:6px;
        display:block;
    }
}
/* Buttons for actions */
.action-btn {
    display: inline-block;
    padding: 6px 14px;
    margin: 5px 5px 10px 0;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    color: #fff;
    transition: all 0.3s ease;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

.action-btn.edit {
    background-color: #1976d2;
}

.action-btn.edit:hover {
    background-color: #1565c0;
    transform: translateY(-2px);
}

.action-btn.delete {
    background-color: #e53935;
}

.action-btn.delete:hover {
    background-color: #c62828;
    transform: translateY(-2px);
}

</style>
</head>
<body>
<button class="toggle-btn" id="toggleBtn">☰</button>

<div class="sidebar" id="sidebar">
<ul>
    <li><a href="dashboard.php">Dashboard</a></li>
    <li><a href="addproduct.php">Add Product</a></li>
    <li><a href="display.php">Update Products</a></li>
    <li><a class="active" href="vieworders.php">View Orders</a></li>
    <li><a href="users.php">Users</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>
</div>

<div class="search-container">
    <input type="text" id="searchBox" placeholder="Search Order ID...">
    <span id="searchBtn">🔍</span>
</div>

<div class="container">
<h2 class="all">All Orders</h2>

<?php if(mysqli_num_rows($result) > 0): ?>
<div class="table-wrapper">
<table>
<thead>
<tr>
    <th>Order ID</th>
    <th>User</th>
    <th>Product</th>
    <th>Image</th>
    <th>Quantity</th>
    <th>Price</th>
    <th>Mobile</th>
    <th>Email</th>
    <th>Full Address</th>
    <th>Pincode</th>
    <th>Payment</th>
    <th>Status</th>
    <th>Order Status</th>
    <th>Order Date</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>
<?php while($row = mysqli_fetch_assoc($result)):
    $orderDate = strtotime($row['order_date']);
    $isNew = (time() - $orderDate) <= 10 * 60 * 60; // 24 hours
?>
<tr class="<?= $isNew ? 'new-order' : '' ?>">
    <td><?= htmlspecialchars($row['order_id']) ?> <?php if($isNew): ?><img src="../image/image.png" class="new-icon"><?php endif; ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['product_name']) ?></td>
    <td><img src="../image/<?= htmlspecialchars($row['product_image']) ?>" width="60"></td>
    <td><?= htmlspecialchars($row['quantity']) ?></td>

    <td>₹<?= number_format($row['total_price']) ?></td>
    <td><?= htmlspecialchars($row['mobile']) ?></td>
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td><?= htmlspecialchars($row['house_no'] . ', ' . $row['full_address'] . ', ' . $row['landmark'] . ', ' . $row['state']) ?></td>
    <td><?= htmlspecialchars($row['pincode']) ?></td>

    <td><span class="status <?= htmlspecialchars($row['payment_status']) ?>"><?= htmlspecialchars($row['payment_status']) ?></span></td>
    <td>
        <span class="status <?= htmlspecialchars($row['order_status']) ?>"><?= htmlspecialchars($row['order_status']) ?></span>
    </td>
    <td>
        <select class="status-dropdown" onchange="updateStatus('<?= $row['order_id'] ?>', this.value)">
            <option value="Pending" <?= ($row['order_status']=='Pending')?'selected':'' ?>>Pending</option>
            <option value="Processing" <?= ($row['order_status']=='Processing')?'selected':'' ?>>Processing</option>
            <option value="Delivered" <?= ($row['order_status']=='Delivered')?'selected':'' ?>>Delivered</option>
            <option value="Cancelled" <?= ($row['order_status']=='Cancelled')?'selected':'' ?>>Cancelled</option>
        </select>
    </td>
    <td><?= date('d-m-Y h:i A', strtotime($row['order_date'])) ?></td>
<td>
    <a href="edit_order.php?order_id=<?= $row['order_id'] ?>" class="action-btn edit">Edit</a>
    <a href="delete_order.php?order_id=<?= $row['order_id'] ?>" onclick="return confirm('Are you sure you want to delete this order?');" class="action-btn delete">Delete</a>
</td>

</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
<?php else: ?>
<p style="text-align:center;">No orders found.</p>
<?php endif; ?>
</div>

<script>
const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('toggleBtn');

toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('show');
});

function updateStatus(orderId, newStatus) {
    if(confirm("Are you sure you want to change the status to " + newStatus + "?")) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "update_status.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if(this.responseText.trim() === "success"){
                alert("Order status updated successfully!");
                location.reload();
            } else {
                alert("Failed to update status!");
            }
        };
        xhr.send("order_id=" + orderId + "&status=" + newStatus);
    }
}

// Live search
const searchBox = document.getElementById('searchBox');
searchBox.addEventListener('keyup', function() {
    const filter = searchBox.value.toUpperCase();
    const table = document.querySelector('.table-wrapper table tbody');
    const tr = table.getElementsByTagName('tr');
    for (let i = 0; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName('td')[0];
        if(td){
            const txtValue = td.textContent || td.innerText;
            tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? '' : 'none';
        }
    }
});
</script>
</body>
</html>
