<?php
session_start();
include "db.php"; // make sure db.php has your DB connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop</title>

  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #fce4ec;
      color: #333;
    }

    .header {
      background-color: #fff;
      padding: 1rem 2rem;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header a {
      text-decoration: none;
      color: #c2185b;
      font-size: 1.5rem;
      font-weight: bold;
    }

    .header nav ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      gap: 1.5rem;
    }

    .header nav a {
      font-size: 1rem;
      color: #f06292;
      transition: color 0.3s;
    }

    .header nav a:hover {
      color: #c2185b;
    }

    .main {
      padding: 2rem;
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 2rem;
    }

    .product {
      background-color: #fff;
      padding: 1.5rem;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      text-align: center;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .product:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    /* Image wrapper to hold stock badge */
    .image-wrapper {
      position: relative;
      margin-bottom: 1rem;
      display: inline-block;
      line-height: 0;
    }

    .product img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 4px;
    }

    /* Stock Notification Styling */
    .stock-badge {
      position: absolute;
      bottom: 0;
      right: 0;
      padding: 8px 10px;
      border-radius: 5px 0 8px 0;
      font-size: 10px;
      font-weight: bold;
      color: white;
      text-transform: uppercase;
      z-index: 10;
    }

    .stock-low {
      background-color: #ffc107; /* Yellow for low stock (1-4) */
    }

    .stock-few {
      background-color: #fd7e14; /* Orange for 5 only */
    }

    .stock-zero {
      background-color: #dc3545; /* Red for out of stock */
    }

    .product h2 {
      font-size: 1.25rem;
      margin-top: 0;
      margin-bottom: 0.5rem;
    }

    .product p {
      margin: 0.25rem 0;
      color: #777;
    }

    .product a {
      display: inline-block;
      margin-top: 1rem;
      padding: 0.75rem 1.5rem;
      background-color: #f06292;
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      transition: background-color 0.3s;
    }

    .product a:hover {
      background-color: #c2185b;
    }

    .description {
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
      margin: 5px 0;
    }

    .show-more {
      color: #e1628c;
      cursor: pointer;
      display: inline-block;
      margin-top: 5px;
    }

    .show-more:hover {
      color: #b82757;
    }

    .footer {
      background-color: #f1a8c0;
      color: #fff;
      text-align: center;
      padding: 1rem;
      margin-top: 2rem;
    }
  </style>
</head>
<body>

  <header class="header">
    <a href="index.php">Shop</a>
    <nav>
      <ul>
        <?php if (!isset($_SESSION['user_id'])) { ?>
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php">Sign Up</a></li>
        <?php } else { ?>
          <li><a href="admin/dashboard.php">Dashboard</a></li>
        <?php } ?>
      </ul>
    </nav>
  </header>

  <main class="main">
    <?php
    $sql = "SELECT * FROM products";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
        $image = !empty($row['image']) ? "image/" . $row['image'] : "default.png";
        $stock = (int)$row['stock'];

        // Determine stock status
        $stock_text = '';
        $stock_class = '';

        if ($stock == 0) {
          $stock_text = 'Out of Stock';
          $stock_class = 'stock-zero';
        } elseif ($stock <= 5 && $stock > 0) {
          $stock_text = $stock . ' Only!';
          $stock_class = ($stock == 5) ? 'stock-few' : 'stock-low';
        }
    ?>
        <div class="product">
          <div class="image-wrapper">
            <img src="<?php echo htmlspecialchars($image); ?>" alt="Product: <?php echo htmlspecialchars($row['name']); ?>">
            <?php if (!empty($stock_text)) { ?>
              <span class="stock-badge <?php echo $stock_class; ?>">
                <?php echo $stock_text; ?>
              </span>
            <?php } ?>
          </div>

          <h2><?php echo htmlspecialchars($row['name']); ?></h2>
          <p class="description"><?php echo htmlspecialchars($row['description']); ?></p>
          <span class="show-more" onclick="toggleDescription(this)">Show more</span>
          <p>Price: ₹<?php echo number_format($row['price']); ?></p>

          <?php if (isset($_SESSION['user_id']) && $stock > 0) { ?>
            <a href="singleorder.php?user_id=<?php echo urlencode($_SESSION['user_id']); ?>&product_id=<?php echo urlencode($row['id']); ?>&product_price=<?php echo urlencode($row['price']); ?>">Buy Now</a>
          <?php } elseif ($stock <= 0) { ?>
            <a href="#" style="background-color: #6c757d; cursor: not-allowed;" onclick="return false;">Out of Stock</a>
          <?php } else { ?>
            <a href="login.php">Buy Now</a>
          <?php } ?>
        </div>
    <?php
      }
    } else {
      echo "<p>No products available</p>";
    }
    ?>
  </main>

  <footer class="footer">
    <p>&copy; 2025 Softlogic</p>
  </footer>

  <script>
    function toggleDescription(element) {
      const desc = element.previousElementSibling;
      if (desc.style.webkitLineClamp === "unset") {
        desc.style.webkitLineClamp = "3";
        element.innerText = "Show more";
      } else {
        desc.style.webkitLineClamp = "unset";
        element.innerText = "Show less";
      }
    }
  </script>
</body>
</html>
