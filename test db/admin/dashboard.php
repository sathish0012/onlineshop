<?php
session_start();
include "../db.php";

// Only allow logged-in admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch dashboard stats
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total_users FROM users"))['total_users'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total_orders FROM orders"))['total_orders'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(o.total_price) AS total_revenue FROM orders o JOIN products p ON o.product_id = p.id"))['total_revenue'] ?? 0;
$today_collection = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(o.total_price) AS today_collection FROM orders o JOIN products p ON o.product_id = p.id WHERE DATE(o.order_date) = CURDATE()"))['today_collection'] ?? 0;
$weekly_collection = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(o.total_price) AS weekly_collection FROM orders o JOIN products p ON o.product_id = p.id WHERE o.order_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)"))['weekly_collection'] ?? 0;
$monthly_collection = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(o.total_price) AS monthly_collection FROM orders o JOIN products p ON o.product_id = p.id WHERE MONTH(o.order_date)=MONTH(CURDATE()) AND YEAR(o.order_date)=YEAR(CURDATE())"))['monthly_collection'] ?? 0;

// Fetch all products for dashboard
// Fetch all products for dashboard, sorted by stock (lowest first)
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY stock ASC, id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<style>
/* General body */
body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #e8f0fe;
    color: #1c1c1c;
}

/* Sidebar */
.sidebar {
    width: 220px;
    background: #1a237e;
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    padding-top: 55px;
    box-shadow: 2px 0 8px rgba(0,0,0,0.15);
    transition: transform 0.3s ease;
    z-index: 1000;
}
.sidebar ul { list-style: none; padding: 0; margin: 0; }
.sidebar li { margin-bottom: 10px; }
.sidebar a {
    display: block;
    padding: 12px 25px;
    text-decoration: none;
    color: #e8eaf6;
    font-size: 16px;
    border-radius: 8px;
    transition: 0.3s;
}
.sidebar a.active,
.sidebar a:hover {
    background: #3949ab;
    color: #fff;
}

/* Toggle button for mobile */
.toggle-btn {
    display: none;
    position: fixed;
    top: 10px;
    left: 10px;
    background: #3949ab;
    color: #fff;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    z-index: 1001;
    font-size: 18px;
}

/* Main content */
.content {
    margin-left: 240px;
    padding: 20px;
}

/* Summary cards */
.summary-cards {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 30px;
    justify-content: center;
}
.card {
    flex: 1;
    min-width: 180px;
    padding: 20px;
    border-radius: 8px;
    background: #fff;
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    text-align: center;
    border-left: 5px solid #3949ab;
    transition: 0.3s;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.15);
}
.card h3 {
    margin: 0 0 10px;
    color: #1a237e;
    font-size: 1rem;
    text-transform: uppercase;
}
.card p {
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0;
    color: #333;
}
.card.revenue { border-left-color: #43a047; }
.card.users { border-left-color: #f44336; }

/* Product grid */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
}

/* Product card */
.product-card {
    background: #fff;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    text-align: center;
    position: relative;
    transition: 0.3s;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    min-height: 300px;
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.15);
}

/* Product image */
.product-card img {
    width: 100%;
    height:fit-content;
    object-fit: cover;
    border-radius: 6px;
    margin-bottom: 10px;
}
.product-name {
  font-size: 1.1rem;
  font-weight: 600;
  margin: 0 0 5px;
  display: -webkit-box;
  -webkit-line-clamp: 3;        /* show only 3 lines */
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  transition: all 0.3s ease;
}

/* when expanded */
.product-name.expanded {
  -webkit-line-clamp: unset;
  overflow: visible;
}

/* show-more button style */
.show-more {
  color:  #43a047;
  cursor: pointer;
  font-size: 0.8rem;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.3s ease;
}

.show-more:hover {
  color:  #1f5d22ff;
}


/* Product title and price */
.product-card h4 {
    margin: 5px 0;
    color: #1a237e;
    font-size: 1.1rem;
}
.product-card p {
    margin: 5px 0;
    color: #555;
    font-weight: bold;
}

/* Stock badges */
.stock {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 12px;
    font-weight: bold;
    color: #fff;
}
.stock-zero { background: #dc3545; }
.stock-low { background: #fd7e14; }
.stock-few { background: #ffc107; }

/* Blinking low stock animation (multi-color) */
@keyframes smoothColorBlink {
    0%   { color: #fd7e14; }
    33%  { color: #ff3d00; }
    66%  { color: #ffc107; }
    100% { color: #fd7e14; }
}
.low-stock-blink {
    font-weight: bold;
    animation: smoothColorBlink 1.2s infinite;
}

/* Buttons same line */
.button-group {
    display: flex;
    gap: 10px;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 10px;
}
.edit,
.delete {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 5px;
    font-weight: bold;
    text-decoration: none;
    transition: 0.3s;
}
.edit { background: #3949ab; color: #fff; }
.edit:hover { background: #1a237e; }
.delete { background: #f44336; color: #fff; }
.delete:hover { background: #d32f2f; }


.icon {
    color: yellow;
    width: 40px !important;
    height: 40px !important;
    margin-left: 6px;
    vertical-align: middle;
    transition: transform 0.3s ease, opacity 0.3s ease;
    animation: pulse 1.2s infinite;
}
@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.15);
    }
    75% {
        transform: scale(1.25);
    }
     100% {
        transform: scale(1.15);
    }
}

/* Add a slight hover effect (optional) */
.icon:hover {
    transform: scale(1.1);
    opacity: 0.9;
}

/* Responsive for mobile */
@media (max-width: 768px) {
    .icon {
        width: 65px;
        margin-left: 10px;
        margin-top: 5px;
        display: inline-block;
    }
}


/* Responsive */
@media(max-width:1024px){
    .content { margin-left:200px; padding:15px; }
}
@media(max-width:768px){
    .toggle-btn { display:block; }
    .sidebar { transform:translateX(-100%); width:200px; }
    .sidebar.show { transform:translateX(0); }
    .content { margin-left:0; padding:15px; }
    .dash{margin-left: 50px; margin-top: 0px;}
}

/* Container for search input */
.search-container {
    position: relative;
    margin-right: 10%;
    margin-left: 60% !important;
    margin-top: -50px !important;
    text-align: end;
    width: 50%;
    max-width: 400px;   
    margin: 20px auto; /* center horizontally */
}

/* Search input styling */
.product-search {
    margin-top: -10px;
    width: 100%;
    padding: 10px 40px 10px 10px; /* space for icon on right */
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 16px;
    transition: all 0.3s ease;
}

/* Focus effect */
.product-search:focus {
    outline: none;
    border-color: #3949ab;
    box-shadow: 0 0 5px rgba(57, 73, 171, 0.5);
}

/* Search icon inside input */
.search-icon {
    position: absolute;
    right: -30px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 18px;
    color: #888;
    pointer-events: none; /* allows clicking inside input */
    transition: color 0.3s ease;
}

/* Hover effect for icon (optional) */
.product-search:hover + .search-icon,
.product-search:focus + .search-icon {
    color: #3949ab;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .search-container {
        margin-top: 30px !important;
        margin-left: 20px !important;
        width: 80%;
    }
    .product-search {
        font-size: 14px;
        padding: 8px 35px 8px 10px;
    }
    .search-icon {
        font-size: 16px;
        right: 8px;
    }
}

@media (max-width: 480px) {
    .search-container {
        width: 95%;
    }
    .product-search {
        font-size: 14px;
        padding: 8px 35px 8px 10px;
    }
    .search-icon {
        font-size: 16px;
        right: 8px;
    }
}

</style>
</head>
<body>

<button class="toggle-btn" onclick="toggleSidebar()">☰</button>

<div class="sidebar" id="sidebar">
    <ul>
        <li><a class="active" href="dashboard.php">Dashboard</a></li>
        <li><a href="addproduct.php">Add Product</a></li>
        <li><a href="display.php">Update Products</a></li>
        <li><a href="vieworders.php">View Orders</a></li>
        <li><a href="users.php">Users</a></li>
        <li><a ref="settings.php">Settings</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<div class="content">
    <h2 class="dash" >📊 Dashboard Overview</h2>
    <div class="summary-cards">
        <div class="card users"><h3>Total Users</h3><p><?= $total_users ?></p></div>
        <div class="card"><h3>Total Orders</h3><p><?= $total_orders ?></p></div>
        <div class="card"><h3>Today's Collection</h3><p>₹<?= number_format($today_collection,2) ?></p></div>
        <div class="card"><h3>Weekly Collection</h3><p>₹<?= number_format($weekly_collection,2) ?></p></div>
        <div class="card"><h3>Monthly Collection</h3><p>₹<?= number_format($monthly_collection,2) ?></p></div>
        <div class="card revenue"><h3>Total Revenue</h3><p>₹<?= number_format($total_revenue,2) ?></p></div>
    </div>

    <h2>🛒 Products</h2>

<div class="search-container">
    <input type="text" id="productSearch" class="product-search" placeholder="Search products...">
    <span class="search-icon">🔍</span> <!-- Unicode magnifying glass -->
</div>

    <div class="products-grid">
        <?php while($row = mysqli_fetch_assoc($products)):
            $stock = (int)$row['stock'];
            $stock_text = $stock==0?"Out of Stock":($stock<=5?"$stock Only!":"");
            $stock_class = $stock==0?"stock-zero":($stock<=5?"stock-low":"");
        ?>
        <div class="product-card">
            <?php if($stock_text): ?>
                <div class="stock <?= $stock_class ?>"><?= $stock_text ?></div>
            <?php endif; ?>

            <img src="../image/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
            <h4 class="product-name"><?= htmlspecialchars($row['name']) ?></h4>
            <span class="show-more" onclick="toggleText(this)">Show more</span>
            <p>Price: ₹<?= number_format($row['price']) ?></p>
           <p>
    Stock: <?= $stock ?>
    <?php if ($stock <= 5 && $stock > 0): ?>
        <img src="../image/low stock.png" alt="Low Stock" class="icon" >
    <?php elseif ($stock == 0): ?>
        <span style="color:#dc3545; font-weight:bold;">(Out of Stock)</span>
    <?php endif; ?>
</p>

            <div class="button-group">
                <a class="edit" href="updateproduct.php?product_id=<?= $row['id'] ?>">Edit</a>
                <a class="delete" href="deleteproduct.php?product_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
function toggleText(btn) {
  const title = btn.previousElementSibling; // <h4> element
  title.classList.toggle('expanded');
  btn.textContent = title.classList.contains('expanded') ? 'Show less' : 'Show more';
}

function toggleSidebar(){
    document.getElementById('sidebar').classList.toggle('show');
}
// Toggle product name expand
function toggleText(btn) {
  const title = btn.previousElementSibling; // <h4> element
  title.classList.toggle('expanded');
  btn.textContent = title.classList.contains('expanded') ? 'Show less' : 'Show more';
}

// Toggle sidebar
function toggleSidebar(){
    document.getElementById('sidebar').classList.toggle('show');
}

// Product search filter
const searchInput = document.getElementById('productSearch');
searchInput.addEventListener('keyup', function() {
    const filter = searchInput.value.toLowerCase();
    const products = document.querySelectorAll('.product-card');
    products.forEach(product => {
        const name = product.querySelector('.product-name').textContent.toLowerCase();
        if (name.includes(filter)) {
            product.style.display = 'flex';
        } else {
            product.style.display = 'none';
        }
    });
});


</script>

</body>
</html>
