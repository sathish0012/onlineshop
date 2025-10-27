<?php
session_start();
include "../db.php";

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {

        // Fetch categories
        $sql1 = "SELECT * FROM categires"; 
        $result1 = mysqli_query($conn, $sql1);

        $product_id = "";
        $row2 = [];

        if (isset($_GET['product_id'])) {
            $product_id = $_GET['product_id'];
            $sql2 = "SELECT * FROM products WHERE id = '$product_id'";
            $result2 = mysqli_query($conn, $sql2);
            $row2 = mysqli_fetch_assoc($result2);
        }

        // Update product
        if (isset($_POST['add_product'])) {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $stock = $_POST['stock'];
            $category_name = $_POST['category_name'];

            $image = $_FILES['image']['name'];
            $temp_location = $_FILES['image']['tmp_name']; 
            $upload_location = "../image/";

            if (!empty($image)) {
                move_uploaded_file($temp_location, $upload_location . $image);
                $sql3 = "UPDATE products SET 
                            name = '$name',
                            description = '$description',
                            price = '$price',
                            stock = '$stock',
                            image = '$image',
                            category_name = '$category_name'
                         WHERE id = '$product_id'";
            } else {
                $sql3 = "UPDATE products SET 
                            name = '$name',
                            description = '$description',
                            price = '$price',
                            stock = '$stock',
                            category_name = '$category_name'
                         WHERE id = '$product_id'";
            }

            $result3 = mysqli_query($conn, $sql3);

            if ($result3) {
                header("Location: display.php");
                exit();
            } else {
                echo "<div class='error-message'>Error!: " . $conn->error . "</div>";
            }
        }

    } else {
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
<title>Update Product</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #e8f0fe;
    display: flex;
    min-height: 100vh;
    color: #1c1c1c;
}

/* Sidebar */
.sidebar {
    width: 220px;
    background-color: #1a237e;
    position: fixed;
    top:0;
    left:0;
    height:100vh;
    padding-top:60px;
    box-shadow:2px 0 8px rgba(0,0,0,0.15);
    transition: transform 0.3s ease;
    z-index:1000;
}
.sidebar ul { list-style:none; padding:0; margin:0; }
.sidebar li { margin-bottom:10px; }
.sidebar a {
    display:block;
    padding:12px 25px;
    text-decoration:none;
    color:#e8eaf6;
    font-size:16px;
    border-radius:8px;
    transition:0.3s;
}
.sidebar a:hover, .sidebar a.active { background-color:#3949ab; color:#fff; }

/* Toggle button */
.toggle-btn {
    display:none;
    position: fixed;
    top:15px;
    left:15px;
    background: #3949ab;
    color:#fff;
    border:none;
    padding:10px 15px;
    border-radius:5px;
    cursor:pointer;
    z-index:1001;
    font-size:18px;
    box-shadow:0 3px 6px rgba(0,0,0,0.2);
}

/* Main content */
.dashbord_main {
    margin-top: 70px;
  margin-bottom: auto;
    margin-left:580px;
    padding:30px;
    background-color:#fff;
    border-radius:12px;
    box-shadow:0 6px 15px rgba(0,0,0,0.1);
    width:100%;
    max-width:500px;
}

/* Form */
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
    border-color:#3949ab;
    box-shadow:0 0 5px rgba(57,73,171,0.4);
    outline:none;
}
textarea { resize: vertical; min-height:100px; }
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
input[type="submit"]:hover { background-color:#1a237e; transform:translateY(-2px); }

img { margin-bottom:10px; border-radius:6px; }

/* Messages */
.success-message, .error-message {
    padding:10px 20px;
    border-radius:8px;
    margin-bottom:15px;
    font-weight:bold;
    text-align:center;
}
.success-message { background-color:#d4edda; color:#155724; }
.error-message { background-color:#f8d7da; color:#721c24; }

/* Responsive */
@media(max-width:768px){
    .toggle-btn { display:block; }
    .sidebar { transform:translateX(-100%); width:200px; }
    .sidebar.show { transform:translateX(0); }
    .dashbord_main { margin-left:20px; padding:20px; width:90%; }
}
</style>
</head>
<body>

<button class="toggle-btn" onclick="toggleSidebar()">☰</button>

<div class="sidebar" id="sidebar">
<ul>
    <li><a  href="dashboard.php">Dashboard</a></li>
    <li><a href="addproduct.php">Add Product</a></li>
    <li><a class="active" href="display.php">Update Products</a></li>
    <li><a href="vieworders.php">View Orders</a></li>
    <li><a href="users.php">Users</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>
</div>

<div class="dashbord_main">
    <form action="updateproduct.php?product_id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data">
      <input type="text" name="name" placeholder="Enter Product Name!" value="<?php echo $row2['name'] ?? ''; ?>">
      <textarea name="description" placeholder="Enter Product Description"><?php echo $row2['description'] ?? ''; ?></textarea>
      <input type="number" name="price" placeholder="Enter Price Here!" value="<?php echo $row2['price'] ?? ''; ?>">
      <input type="number" name="stock" placeholder="Enter Stock Number!" value="<?php echo $row2['stock'] ?? ''; ?>">
      
      <?php if (!empty($row2['image'])) { ?>
        <img src="../image/<?php echo $row2['image']; ?>" alt="" width="100">
      <?php } ?>
      
      <input type="file" name="image">

      <select name="category_name">
        <option value="">Select Category Name</option>
        <?php 
        $result2 = mysqli_query($conn, "SELECT * FROM categires");
        while ($rowCat = mysqli_fetch_assoc($result2)) { 
            $selected = ($row2['category_name'] ?? '') == $rowCat['name'] ? "selected" : "";
        ?>
            <option value="<?php echo $rowCat['name']; ?>" <?php echo $selected; ?>>
                <?php echo $rowCat['name']; ?>
            </option>
        <?php } ?>
      </select>

      <input type="submit" name="add_product" value="Update Product">
    </form>
</div>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('show');
}
</script>

</body>
</html>
