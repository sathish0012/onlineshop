<?php
session_start();
include "../db.php";

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {

    // Fetch categories
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
<title>View Orders</title>
<style>
body {
  background-color: #fce4ec;
  font-family: Arial, sans-serif;
  margin: 0;
}

/* Sidebar */
.dashbord_slider {
  width: 200px;
  height: 100vh;
  background-color: #f4f4f4;
  padding-top: 20px;
  box-shadow: 2px 0 5px rgba(0,0,0,0.1);
  position: fixed;
  top: 0;
  left: 0;
}

.dashbord_slider ul {
  list-style: none;
  padding: 10px;
  margin: 0;
}

.dashbord_slider li { margin-bottom: 10px; }

.dashbord_slider a {
  display: block;
  padding: 12px 15px;
  text-decoration: none;
  color: #333;
  font-size: 16px;
  border-radius: 5px;
  transition: background 0.3s, color 0.3s;
}

.dashbord_slider a:hover {
  background-color: #e6c9d2;
  color: #fff;
}

/* Table */
.table-container {
  width: calc(100% - 200px);
  margin-left: 200px;
  background-color: #fff;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  border-radius: 8px;
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.9em;
  min-width: 800px;
}

thead { background-color: #f2f2f2; }

th, td { padding: 12px 15px; border: 1px solid #ddd; text-align: left; }

tbody tr:nth-child(even) { background-color: #f9f9f9; }

tbody tr:hover { background-color: #f1f1f1; }

table img { width: 300px; height: 300px; object-fit: cover; border-radius: 4px; }

.update, .delete {
  text-decoration: none;
  padding: 6px 10px;
  border-radius: 4px;
  font-weight: bold;
  font-size: 15px;
}

.update { background-color: #4caf50; color: white; }
.delete { background-color: #f44336; color: white; }
.update:hover, .delete:hover { opacity: 0.8; }

/* Description */
.description-container {
  max-width: 300px;
  position: relative;
}

.description-short {
  display: -webkit-box;
  -webkit-line-clamp: 4; /* show only 4 lines */
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.description-container.expanded .description-short {
  -webkit-line-clamp: unset;
}

.toggle-desc {
  color: blue;
  cursor: pointer;
  font-size: 0.9em;
  display: inline-block;
  margin-top: 5px;
}

.name { width: 150px; }
</style>
</head>
<body>

<div class="dashbord_slider">
  <ul>
    <li><a href="dashboard.php">Dashboard</a></li>
    <li><a href="addproduct.php">Add Product</a></li>
    <li><a href="display.php">Update Product</a></li>
    <li><a href="vieworders.php">View Orders</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
</div>

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
  <th>Action</th>
  <th>Action</th>
</tr>
</thead>
<tbody>
<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
  <td class="name"><?php echo $row['name'] ?></td>
  <td>
    <div class="description-container">
      <p class="description-short"><?php echo $row['description']; ?></p>
      <span class="toggle-desc">See more</span>
    </div>
  </td>
  <td><?php echo $row['price'] ?></td>
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

<script>
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
