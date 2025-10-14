<?php
session_start();
include "../db.php";

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {

        // Fetch categories
        $sql1 = "SELECT * FROM categires"; 
        $result1 = mysqli_query($conn, $sql1);

        $product_id = "";
        $row2 = [];

        // Get product details if product_id is provided
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
            $category_id = $_POST['category_id'];
            $category_name = $_POST['category_name'];

            // Handle image upload
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
                echo "Error!: " . $conn->error;
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
    <title>Admin Dashboard</title>
    <style>
        
body {
  background-color: #fce4ec; /* Light pink background */
  font-family: Arial, sans-serif;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  margin: 0;
}

/* Container for the slider */
.dashbord_slider {
  width: 200px; /* Adjust the width as needed */
  height: 100vh; /* Full height of the viewport */
  background-color: #f4f4f4; /* Light gray background */
  padding-top: 20px;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
  position: fixed; /* Keep it in place while the rest of the page scrolls */
  top: 0;
  left: 0;
}

/* List styling */
.dashbord_slider ul {
  list-style-type: none; /* Remove bullet points */
  padding: 10px;
  margin: 0;
  align-items: center;
}

/* List item styling */
.dashbord_slider li {
  margin-bottom: 10px;
}

/* Link styling */
.dashbord_slider a {
  display: block; /* Make the entire link clickable */
  padding: 12px 15px;
  text-decoration: none;
  color: #333;
  font-size: 16px;
  transition: background-color 0.3s, color 0.3s;
  border-radius: 5px; /* Optional: adds rounded corners */
}

/* Hover effect */
.dashbord_slider a:hover {
  background-color: #e6c9d2; /* Blue background on hover */
  color: #fff; /* White text on hover */
}

.dashbord_main {
  background-color: #fff;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  width: 100%;
  height: fit-content;
  max-width: 500px;
}

form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

input[type="text"],
input[type="number"],
input[type="file"],
textarea,
select {
  width: 100%;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 16px;
  box-sizing: border-box; /* Ensures padding doesn't affect total width */
  transition: border-color 0.3s, box-shadow 0.3s;
}

input[type="text"]:focus,
input[type="number"]:focus,
input[type="file"]:focus,
textarea:focus,
select:focus {
  border-color: #f06292;
  box-shadow: 0 0 5px #c2185b(0, 123, 255, 0.2);
  outline: none;
}

textarea {
  resize: vertical;
  min-height: 100px;
}

input[type="file"] {
  padding: 10px;
  background-color: #f8f9fa;
  cursor: pointer;
}

input[type="submit"] {
  background-color: #f06292;
  color: #fff;
  padding: 12px 20px;
  border: none;
  border-radius: 8px;
  font-size: 18px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s, transform 0.2s;
}

input[type="submit"]:hover {
  background-color: #c2185b;
  transform: translateY(-2px);
}

/* Media query for screens smaller than 600px */
@media (max-width: 600px) {
  .dashbord_main {
    padding: 20px; /* Reduce padding on smaller screens */
    box-shadow: none; /* Remove box-shadow to make it look cleaner */
    border-radius: 0; /* Optional: Make the corners sharp for a full-width look */
  }

  /* Reduce font sizes for better readability on small screens */
  input[type="text"],
  input[type="number"],
  input[type="file"],
  textarea,
  select,
  input[type="submit"] {
    font-size: 15px;
  }
}

/* This is the corrected 'focus' rule from your original code. The 'box-shadow' had a syntax error. */
input[type="text"]:focus,
input[type="number"]:focus,
input[type="file"]:focus,
textarea:focus,
select:focus {
  border-color: #f06292;
  box-shadow: 0 0 5px rgba(240, 98, 146, 0.4); /* Corrected box-shadow syntax */
  outline: none;
}
p {
  margin-left: 20px;
  margin-top: 10px;
  margin-bottom: 0px;
  padding: 0px;
  font-family: Verdana, Geneva, Tahoma, sans-serif;
}

.success-message {
  background-color: #d4edda; /* Light green */
  color: #155724; /* Dark green text */
  padding: 10px 20px;
  border: 1px solid #c3e6cb;
  border-radius: 8px;
  margin-top: 20px;
  width: 350px; /* Match form width */
  text-align: center;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  font-weight: bold;
}
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
</body>
</html>
