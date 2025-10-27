<?php
session_start();
include "../db.php";

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {

    // Fetch categories
    $sql1 = "SELECT * FROM categires"; 
    $result1 = mysqli_query($conn, $sql1);

    if ($_SESSION['user_role'] === 'admin') {
        if (isset($_POST['add_product'])) { 
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $stock = $_POST['stock'];

            // File upload
            $image = $_FILES['image']['name'];
            $temp_location = $_FILES['image']['tmp_name']; 
            $upload_location = "../image/";

            $category_id = $_POST['category_id'];
            $category_name = $_POST['category_name'];

            $sql = "INSERT INTO products (name, description, price, stock, image, category_id, category_name) 
                    VALUES ('$name', '$description', '$price', '$stock', '$image', '$category_id', '$category_name')";
            $result = mysqli_query($conn, $sql);

            if (!$result) {
                   echo "<script>alert('Error!: $error');</script>";
            } else {
              echo "<script>alert('Product added successfully!');</script>";
              move_uploaded_file($temp_location, $upload_location . $image);
            }
        }
    } else {
        echo "Go for user dashboard";
    }
} 
else {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Product</title>
<style>
/* RESET */
* { margin: 0; padding: 0; box-sizing: border-box; }

/* BODY */
body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #e8f0fe; /* Light blue */
  display: flex;
  min-height: 100vh;
  color: #1c1c1c;
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

.sidebar ul { list-style:none; padding:0; margin:0; }
.sidebar li { margin-bottom: 10px; }
.sidebar a {
  display:block;
  padding:12px 25px;
  text-decoration:none;
  color:#e8eaf6; /* light text */
  font-size:16px;
  border-radius:8px;
  transition:0.3s;
}
.sidebar a:hover, .sidebar a.active {
  background-color:#3949ab;
  color:#fff;
}

/* TOGGLE BUTTON */
.toggle-btn {
  display:none;
  position: fixed;
  top: 15px;
  left: 15px;
  background: #3949ab;
  color: #fff;
  border: none;
  padding: 10px 15px;
  border-radius:5px;
  cursor:pointer;
  z-index: 1001;
  font-size:18px;
  box-shadow: 0 3px 6px rgba(0,0,0,0.2);
}

/* MAIN CONTENT */
.dashbord_main {
  margin-top: 70px;
  margin-bottom: auto;
  margin-left: 580px;
  padding: 30px;
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 6px 15px rgba(0,0,0,0.1);
  width: 100%;
  max-width: 500px;
}

/* FORM */
form { display:flex; flex-direction: column; gap:15px; }
input[type="text"], input[type="number"], input[type="file"], textarea, select {
  width:100%;
  padding:12px;
  font-size:16px;
  border:1px solid #ccc;
  border-radius:8px;
  transition:0.3s;
}
input[type="text"]:focus, input[type="number"]:focus, input[type="file"]:focus, textarea:focus, select:focus {
  border-color: #3949ab;
  box-shadow: 0 0 5px rgba(57, 73, 171, 0.4);
  outline:none;
}
textarea { resize:vertical; min-height:100px; }
input[type="file"] { background-color:#f8f9fa; cursor:pointer; }

input[type="submit"] {
  background-color:#3949ab;
  color:#fff;
  font-size:18px;
  font-weight:bold;
  padding:12px 20px;
  border:none;
  border-radius:8px;
  cursor:pointer;
  transition:0.3s;
}
input[type="submit"]:hover {
  background-color:#1a237e;
  transform: translateY(-2px);
}

/* SUCCESS / ERROR MESSAGE */
.success-message, .error-message {
  padding:10px 20px;
  border-radius:8px;
  margin-bottom:15px;
  font-weight:bold;
  text-align:center;
}
.success-message { background-color:#d4edda; color:#155724; }
.error-message { background-color:#f8d7da; color:#721c24; }

/* MEDIA QUERIES */
@media(max-width:768px){
  .toggle-btn { display:block; }
  .sidebar { transform: translateX(-100%); width:200px; }
  .sidebar.show { transform: translateX(0); }
  .dashbord_main { margin-left:20px; padding:20px; width:90%; }
}
</style>
</head>
<body>

<button class="toggle-btn" onclick="toggleSidebar()">☰</button>

<div class="sidebar" id="sidebar">
<ul>
    <li><a  href="dashboard.php">Dashboard</a></li>
    <li><a class="active" href="addproduct.php">Add Product</a></li>
    <li><a href="display.php">Update Products</a></li>
    <li><a href="vieworders.php">View Orders</a></li>
    <li><a href="users.php">Users</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>
</div>

<div class="dashbord_main">
    <form action="addproduct.php" method="POST" enctype="multipart/form-data">
      <input type="text" name="name" placeholder="Enter Product Name!" required>
      <textarea name="description" placeholder="Enter Product Description" required></textarea>
      <input type="number" name="price" placeholder="Enter Price Here!" required>
      <input type="number" name="stock" placeholder="Enter Stock Number!" required>
      <p>Upload Image Here!</p>
      <input type="file" name="image" required>

      <select name="category_id" required>
        <option value="">Select Category ID</option>
        <?php if(isset($result1)) { while($row = mysqli_fetch_assoc($result1)) { ?>
            <option value="<?php echo $row['id']; ?>"><?php echo $row['id']; ?></option>
        <?php }} ?>
      </select>

      <select name="category_name" required>
        <option value="">Select Category Name</option>
        <?php 
        $result2 = mysqli_query($conn, "SELECT * FROM categires");
        while($row2 = mysqli_fetch_assoc($result2)) { ?>
            <option value="<?php echo $row2['name']; ?>"><?php echo $row2['name']; ?></option>
        <?php } ?>
      </select>

      <input type="submit" name="add_product" value="Add Product">
    </form>
</div>

<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('show');
}
</script>

</body>
</html>
