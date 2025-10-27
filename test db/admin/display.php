<?php
session_start();
include "../db.php";

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {

    // Fetch products
    $sql = "SELECT * FROM products"; 
    $result = mysqli_query($conn, $sql);

    if ($_SESSION['user_role'] !== 'admin') {
        echo "Go for user dashboard";
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Products</title>
<style>
body {
  background-color: #e8f0fe; /* light blue background */
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  margin: 0;
  color: #1c1c1c; /* darker text for contrast */
}

/* SIDEBAR */
.sidebar {
  width: 220px;
  background-color: #1a237e; /* deep indigo */
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
  padding-top: 60px;
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
  color: #e8eaf6; /* light text */
  font-size: 16px;
  border-radius: 8px;
  transition: 0.3s;
}
.sidebar a.active, .sidebar a:hover {
  background-color: #3949ab; /* hover indigo */
  color: #fff;
}

/* TOGGLE BUTTON */
.toggle-btn {
  display: none;
  position: fixed;
  top: 15px;
  left: 15px;
  background: #3949ab; /* indigo */
  color: #fff;
  border: none;
  padding: 10px 15px;
  border-radius: 5px;
  cursor: pointer;
  z-index: 1001;
  font-size: 18px;
}

/* CONTENT */
.content {
  margin-left: -30px;
  padding: 20px;
}

/* TABLE */
.table-container {
  width: calc(100% - 240px);
  margin-left: 240px;
  background-color: #fff;
  box-shadow: 0 6px 15px rgba(0,0,0,0.1);
  border-radius: 12px;
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.9em;
  min-width: 900px;
}

thead { 
  background: linear-gradient(90deg, #3949ab, #5c6bc0); 
  color: #fff;
}
th, td { padding: 12px 15px; border: 1px solid #ddd; text-align: left; }
tbody tr:nth-child(even) { background-color: #e3f2fd; }
tbody tr:hover { background-color: #bbdefb; }

table img { width: 80px; height: 80px; object-fit: cover; border-radius: 6px; }

/* Buttons */
.update, .delete {
  text-decoration: none;
  padding: 6px 10px;
  border-radius: 4px;
  font-weight: bold;
  font-size: 14px;
  transition: 0.3s;
}
.update { background-color: #43a047; color: white; }
.delete { background-color: #e53935; color: white; }
.update:hover, .delete:hover { opacity: 0.85; }

/* Description */
.description-container { max-width: 250px; position: relative; }
.description-short {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.description-container.expanded .description-short { -webkit-line-clamp: unset; }
.toggle-desc {
  color: #1a237e; /* dark indigo */
  cursor: pointer;
  font-size: 0.85em;
  display: inline-block;
  margin-top: 5px;
}

/* Responsive */
@media(max-width:1024px){
  .content { top: 50px; margin-left:200px; padding:15px; }
  .table-container { width: calc(100% - 200px); margin-left:200px; }
}
@media(max-width:768px){
  .toggle-btn { display:block; }
  
  .sidebar {
    transform: translateX(-100%);
    width: 200px;
  }
  .sidebar.show { transform: translateX(0); }

  .content {
    margin-left: 0;
    padding: 20px 15px 15px 15px;
    position: relative;  /* remove top:50px */
    top:50px;
  }

  .table-container {
    width: 100%;
    margin-left: 0;
  }
}


</style>
</head>
<body>

<button class="toggle-btn" onclick="toggleSidebar()">☰</button>

<div class="sidebar" id="sidebar">
<ul>
    <li><a href="dashboard.php">Dashboard</a></li>
    <li><a href="addproduct.php">Add Product</a></li>
    <li><a class="active" href="display.php">Update Products</a></li>
    <li><a href="vieworders.php">View Orders</a></li>
    <li><a href="users.php">Users</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>
</div>

<div class="content">
<div class="table-container">
<table>
<thead>
<tr>
  <th>Product Title</th>
  <th>Product Description</th>
  <th>Price</th>
  <th>Stock</th>
  <th>Image</th>
  <th>Category Name</th>
  <th>Update</th>
  <th>Delete</th>
</tr>
</thead>
<tbody>
<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
  <td><?php echo $row['name'] ?></td>
  <td>
    <div class="description-container">
      <p class="description-short"><?php echo $row['description']; ?></p>
      <span class="toggle-desc">See more</span>
    </div>
  </td>
  <td>₹<?php echo $row['price'] ?></td>
  <td><?php echo $row['stock'] ?></td>
  <td><img src="../image/<?php echo $row['image'] ?>" alt=""></td>
  <td><?php echo $row['category_name'] ?></td>
  <td><a class="update" href="updateproduct.php?product_id=<?php echo $row['id'] ?>">Update</a></td>
  <td><a class="delete" href="deleteproduct.php?product_id=<?php echo $row['id'] ?>">Delete</a></td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
</div>

<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('show');
}

// Description toggle
document.querySelectorAll('.toggle-desc').forEach(function(toggle) {
  toggle.addEventListener('click', function() {
    const container = this.parentElement;
    container.classList.toggle('expanded');
    this.textContent = container.classList.contains('expanded') ? 'See less' : 'See more';
  });
});
</script>

</body>
</html>
